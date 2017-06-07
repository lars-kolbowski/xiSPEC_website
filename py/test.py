#!/usr/bin/env python

from pyteomics import mzid
from pyteomics import mzml
import re
import json
import sys


def add_to_modlist(mod, modlist):
    if mod['name'] in [m['name'] for m in modlist]:
        old_mod = modlist[[m['name'] for m in modlist].index(mod['name'])]
        # check if different mod with different masses exists
        if mod['monoisotopicMassDelta'] != old_mod['monoisotopicMassDelta']:
            mod['name'] += "*"
            modlist = add_to_modlist(mod, modlist)
        else:
            for res in mod['residues']:
                if res not in old_mod['residues']:
                    old_mod['residues'].append(res)
    else:
        modlist.append(mod)

    return modlist


def mzid_to_json(item):
    """
    Function to convert a mzidentml item into xiAnnotator JSON format dict

    Parameters:
    ------------------------
    item: mzidentml item
    """
    JSON_dict = {
                 "Peptides": [],
                 "LinkSite": [],
                 "annotation": {},
                 "peaks": []
                 }


    all_mods = []  # Modifications list
    # # check if cl ids match
    # if len(item['SpectrumIdentificationItem']) > 1:                         # CL Peptide check
    #     cl_identifiers = [x["cross-link spectrum identification item"] for x in item['SpectrumIdentificationItem']]
    #     if len(set(cl_identifiers)) > 1:
    #         print "Error: cross-link identifiers don't match"
    #         break

    pepIndex = 0
    for spectrumId_item in item['SpectrumIdentificationItem']:
        # crosslinkId = spectrumId_item["cross-link spectrum identification item"]    # id of both peptides has to match
        JSON_dict["annotation"]["precursorCharge"] = spectrumId_item['chargeState']
        pepId = spectrumId_item['peptide_ref']
        peptide = mzid_reader.get_by_id(pepId)

        # convert pepsequence to dict
        peptide_dict = {"sequence": []}
        for aa in peptide['PeptideSequence']:
            peptide_dict['sequence'].append({"Modification": "", "aminoAcid": aa})

        # add in modifications

        if 'Modification' in peptide.keys():
            for mod in peptide['Modification']:
                link_index = 0  # TODO: multilink support
                mod_location = mod['location'] - 1
                if 'name' in mod.keys():
                    peptide_dict['sequence'][mod_location]['Modification'] = mod['name']  # TODO: abbreviations?
                    if 'cross-link donor' not in mod.keys():
                        all_mods = add_to_modlist(mod, all_mods)
                        # all_mods.append(mod)                                            # save to all mods list

                        # add CL locations
                if 'cross-link donor' in mod.keys() or 'cross-link acceptor' in mod.keys():
                    JSON_dict['LinkSite'].append({"id": link_index, "peptideId": pepIndex, "linkSite": mod_location})
                    JSON_dict["annotation"]["cross-linker"] = {"modMass": mod['monoisotopicMassDelta']}

            pepIndex += 1

        JSON_dict['annotation']['precursorCharge'] = spectrumId_item['chargeState']
        JSON_dict['annotation']['modifications'] = []
        for mod in all_mods:
            JSON_dict['annotation']['modifications'].append({
                'aminoAcids': mod['residues'],
                'id': mod['name'],
                'mass': mod['monoisotopicMassDelta']
            })

        JSON_dict['Peptides'].append(peptide_dict)

    return JSON_dict

print sys.argv[1]
print sys.argv[2]
index = 0


mzid_file = sys.argv[1]#"test_file_B170317_06_Lumos_ML_IN_205_PMBS3_Tryp_SECFr16.mzid"
mzml_file = sys.argv[2]#"B170317_06_Lumos_ML_IN_205_PMBS3_Tryp_SECFr16.mzML"

mzid_reader = mzid.MzIdentML(mzid_file)
#mzml_reader = mzml.MzML(mzml_file)

premzml = mzml.PreIndexedMzML(mzml_file)


i = 0
for mzid_item in mzid_reader:
    print i
    if i < index:
        i += 1
    else:
        json_dict = mzid_to_json(mzid_item)
        matches = re.findall("([0-9]+)", mzid_item["spectrumID"])
        scanID = int(matches[0])
        print json_dict
        break

if premzml._offset_index.has_key(str(scanID)):
    scan = premzml.get_by_id(str(scanID))
elif premzml._offset_index.has_key('controllerType=0 controllerNumber=1 scan='+str(scanID)):
    scan = premzml.get_by_id('controllerType=0 controllerNumber=1 scan='+str(scanID))

# peaklist
i = 0
while i < len(scan["m/z array"]):
    peak = {
            "mz": scan["m/z array"][i],
            "intensity": scan["intensity array"][i]
    }
    json_dict['peaks'].append(peak)
    i += 1

# fragmentation method
json_dict['annotation']['ions'] = [{"type": "PeptideIon"}]

if 'beam-type collision-induced dissociation' in scan['precursorList']['precursor'][0]['activation'].keys():
    json_dict['annotation']['ions'].append({"type": "BIon"})
    json_dict['annotation']['ions'].append({"type": "YIon"})


print json.dumps(json_dict)

# matches = re.findall("@([A-z]+)]", scan['scanList']['scan'][0]['filter string'])


# for mzml_item in mzml_reader:
#     print mzml_item["index"]
#     if mzml_item["index"] == scanID:
#         print mzml_item
#         break

#json.dumps(JSON_dict)




# "annotation":
# {"fragmentTolerance":{"tolerance":20,"unit":"ppm"},
# "modifications":[{"aminoAcids":["C","F","H","M","N","P","Q","W","Y"],"id":"ox","mass":"15.9949"}],
# "ions":[{"type":"PeptideIon"},{"type":"BIon"},{"type":"YIon"}],
# "cross-linker":{"modMass":138.06807961},
# "precursorCharge":3}

# "LinkSite":[{"id":0,"peptideId":0,"linkSite":11},{"id":0,"peptideId":1,"linkSite":0}]
# {"Peptides":[{"sequence":[{"aminoAcid":"K","Modification":""},{"aminoAcid":"Q","Modification":""},{"aminoAcid":"E","Modification":""},{"aminoAcid":"Q","Modification":""},{"aminoAcid":"E","Modification":""},{"aminoAcid":"S","Modification":""},{"aminoAcid":"L","Modification":""},{"aminoAcid":"G","Modification":""},{"aminoAcid":"S","Modification":""},{"aminoAcid":"N","Modification":""},{"aminoAcid":"S","Modification":""},{"aminoAcid":"K","Modification":""}]}