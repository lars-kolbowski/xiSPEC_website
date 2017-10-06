#!/usr/bin/env python

import pyteomics.mzid as py_mzid
import pymzml
import re
import json
import sys
import sqlite3
import os
import shutil
import logging

dev = False

if dev:
    logDir = "log/parser.log"
else:
    logDir = "../log/parser.log"

logging.basicConfig(filename=logDir, level=logging.DEBUG,
                    format='%(asctime)s %(levelname)s %(name)s %(message)s')
logger = logging.getLogger(__name__)

import ntpath


def path_leaf(path):
    head, tail = ntpath.split(path)
    return tail or ntpath.basename(head)


def write_to_db(multiple_inj_list, cur):
    cur.executemany("""
INSERT INTO jsonReqs (
    'id',
    'json',
    'mzid',
    'pep1',
    'pep2',
    'linkpos1',
    'linkpos2',
    'charge',
    'passThreshold',
    'rank',
    'scores',
    'isDecoy',
    'protein',
    'file',
    'scanID'
)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)""", multiple_inj_list)
    return


def add_to_modlist(mod, modlist):
    if mod['name'] == "unknown_modification":
        mod['name'] = "({0:.2f})".format(mod['monoisotopicMassDelta'])

    if mod['name'] in [m['name'] for m in modlist]:
        old_mod = modlist[[m['name'] for m in modlist].index(mod['name'])]
        # check if modname with different mass exists already
        if mod['monoisotopicMassDelta'] != old_mod['monoisotopicMassDelta']:
            # if mod['name'].startswith("unk_mod"):
            #     mod['name'] = mod['name'].translate(None, '0123456789') + str(int(filter(str.isdigit, mod['name'])) + 1)
            # else:
            mod['name'] += "*"
            add_to_modlist(mod, modlist)
        else:
            for res in mod['residues']:
                if res not in old_mod['residues']:
                    old_mod['residues'].append(res)
    else:
        modlist.append(mod)

    return mod['name']

# modlist_test = [
#     {'name': "bs3nh2", 'monoisotopicMassDelta': 123, 'residues': ["A"]}
# ]
# mod1_test = {'name': "unknown_modification", 'monoisotopicMassDelta': 123, 'residues': ["B"]}
# mod2_test = {'name': "bs3nh2", 'monoisotopicMassDelta': 1232, 'residues': ["B"]}
# mod3_test = {'name': "bs3nh2", 'monoisotopicMassDelta': 12323, 'residues': ["A"]}
# print add_to_modlist(mod1_test, modlist_test)
# print modlist_test
# print add_to_modlist(mod2_test, modlist_test)
# print modlist_test
# print add_to_modlist(mod3_test, modlist_test)
# print modlist_test


def get_peaklist_from_mzml(scan):
    """
    Function to extract peaklist in xiAnnotator JSON format dict from mzml

    Parameters:
    ------------------------
    scan, pymzml reader spectrum
    id: scanID
    """

    if "profile spectrum" in scan.keys():
        peaks = scan.centroidedPeaks
    else:
        peaks = scan.centroidedPeaks

    return [{"mz": peak[0], "intensity": peak[1]} for peak in peaks]


def mzid_to_json(item, mzidreader):
    """
    Function to convert a mzidentml item into xiAnnotator JSON format dict

    Parameters:
    ------------------------
    item: mzidentml item
    """
    JSON_dict = {
        "Peptides": [],
        "LinkSite": [],
        "annotation": {"cross-linker": {"modMass": 0}},  # necessary for xiAnn atm-> told Lutz about it
        "peaks": []
    }

    all_mods = []  # Modifications list
    mod_aliases = {
        "amidated_bs3": "bs3nh2",
        "carbamidomethyl": "cm",
        "hydrolyzed_bs3": "bs3oh",
        "oxidation": "ox"
    }

    # # check if cl ids match
    # if len(item['SpectrumIdentificationItem']) > 1:                         # CL Peptide check
    #     cl_identifiers = [x["cross-link spectrum identification item"] for x in item['SpectrumIdentificationItem']]
    #     if len(set(cl_identifiers)) > 1:
    #         print "Error: cross-link identifiers don't match"
    #         break

    pepIndex = 0
    targetDecoy = []
    for spectrumId_item in item:    # len = 1 for linear
        # crosslinkId = spectrumId_item["cross-link spectrum identification item"]    # id of both peptides has to match
        JSON_dict["annotation"]["precursorCharge"] = spectrumId_item['chargeState']
        pepId = spectrumId_item['peptide_ref']
        peptide = mzidreader.get_by_id(pepId)
        peptideEvidences = [mzidreader.get_by_id(s['peptideEvidence_ref']) for s in spectrumId_item['PeptideEvidenceRef']]

        #ToDo: isDecoy might not be defined. How to handle? (could make use of pyteomics.mzid.is_decoy())
        try:
            decoy = peptideEvidences[0]['isDecoy']
        except KeyError:
            decoy = None
        targetDecoy.append({"peptideId": pepIndex, 'isDecoy': decoy}) # TODO: multiple PeptideEvidenceRefs TD?

        proteins = [mzidreader.get_by_id(p['dBSequence_ref']) for p in peptideEvidences]
        accessions = [p['accession'] for p in proteins]
        # convert pepsequence to dict
        peptide_dict = {"sequence": []}
        for aa in peptide['PeptideSequence']:
            peptide_dict['sequence'].append({"Modification": "", "aminoAcid": aa})

        # add in modifications

        if 'Modification' in peptide.keys():
            for mod in peptide['Modification']:
                link_index = 0  # TODO: multilink support
                mod_location = mod['location'] - 1
                if 'residues' not in mod:
                    mod['residues'] = peptide['PeptideSequence'][mod_location]

                if 'name' in mod.keys():
                    # fix mod names
                    mod['name'] = mod['name'].lower()
                    mod['name'] = mod['name'].replace(" ", "_")
                    if mod['name'] in mod_aliases.keys():
                        mod['name'] = mod_aliases[mod['name']]
                    if 'cross-link donor' not in mod.keys():
                        mod['name'] = add_to_modlist(mod, all_mods)  # save to all mods list and get back new_name
                        peptide_dict['sequence'][mod_location]['Modification'] = mod['name']  # TODO: abbreviations?

                        # add CL locations
                if 'cross-link donor' in mod.keys() or 'cross-link acceptor' in mod.keys():
                    JSON_dict['LinkSite'].append(
                        {"id": link_index, "peptideId": pepIndex, "linkSite": mod_location - 1})
                if 'cross-link donor' in mod.keys():
                    JSON_dict["annotation"]["cross-linker"] = {"modMass": mod['monoisotopicMassDelta']}

            pepIndex += 1

        JSON_dict['annotation']['precursorCharge'] = spectrumId_item['chargeState']
        JSON_dict['annotation']['isDecoy'] = targetDecoy
        JSON_dict['annotation']['proteins'] = accessions
        JSON_dict['annotation']['modifications'] = []
        for mod in all_mods:
            JSON_dict['annotation']['modifications'].append({
                'aminoAcids': mod['residues'],
                'id': mod['name'],
                'mass': mod['monoisotopicMassDelta']
            })

        JSON_dict['Peptides'].append(peptide_dict)

    return JSON_dict


# print sys.argv[1]
# print sys.argv[2]
# print sys.argv[3]

if not dev:
    dbfolder = "../../dbs/tmp/"
    try:
        os.stat(dbfolder)
    except:
        os.mkdir(dbfolder)

try:
    if dev:
        con = sqlite3.connect('test.db')
    else:
        con = sqlite3.connect(dbfolder + sys.argv[3] + '.db')
    cur = con.cursor()
    cur.execute("DROP TABLE IF EXISTS jsonReqs")
    cur.execute(
        "CREATE TABLE jsonReqs(id INT PRIMARY KEY, "
        "json TEXT, "
        "mzid TEXT, "
        "pep1 TEXT, "
        "pep2 TEXT, "
        "linkpos1 INT, "
        "linkpos2 INT, "
        "charge INT, "
        "passThreshold INT, "
        "rank INT, "
        "scores TEXT, "
        "isDecoy INT, "
        "protein TEXT, "
        "file TEXT, "
        "scanID INT)")
    # cur.execute("DROP TABLE IF EXISTS mzids")
    # cur.execute("CREATE TABLE mzids (id INT PRIMARY KEY, mzid TEXT)")

except sqlite3.Error, e:
    logger.error(e)
    print json.dumps({"error": e.args[0]})
    sys.exit(1)

returnJSON = {
    "response": "",
    "errors": []
}

try:
    if dev:
        baseDir = "/data/rappstore/users/lkolbowski/xiSPEC/"
        mzid_file = baseDir+"DSSO_B170808_08_Lumos_LK_IN_90_HSA-DSSO-Sample_Xlink-CID-EThcD_CID-only.mzid"
        mzml_file = baseDir+"B170808_08_Lumos_LK_IN_90_HSA-DSSO-Sample_Xlink-CID-EThcD.mzML"

        # mzid_file = baseDir+"PD_B170808_08_Lumos_LK_IN_90_HSA-DSSO-Sample_Xlink-CID-EThcD-(2).mzid"
        # mzml_file = baseDir+"B170808_08_Lumos_LK_IN_90_HSA-DSSO-Sample_Xlink-CID-EThcD.mzML"
        # mzid_file = "B160803_02_Lumos_LK_IN_190_PC_BS3_ETciD_DT_1.mzid"
        # mzml_file = "B160803_02_Lumos_LK_IN_190_PC_BS3_ETciD_DT_1.mzML"
    else:
        mzid_file = sys.argv[1]
        mzml_file = sys.argv[2]
        upload_folder = "../../uploads/" + sys.argv[3]

    mzid_reader = py_mzid.MzIdentML(mzid_file)
    #premzml = mzml.PreIndexedMzML(mzml_file)

    pymzmlReader = pymzml.run.Reader(mzml_file)

    mz_index = 0
    specIdItem_index = 0
    multipleInjList_jsonReqs = []


    # mzid_item = mzid_reader.next()
    for mzid_item in mzid_reader:
        # find pairs of cross-linked items
        CLSpecIdItemSet = set()
        linear_index = -1   # negative index values for linear peptides

        info = {}

        for specIdItem in mzid_item['SpectrumIdentificationItem']:
            if 'cross-link spectrum identification item' in specIdItem.keys():
                CLSpecIdItemSet.add(specIdItem['cross-link spectrum identification item'])
            else:  # assuming linear
                specIdItem['cross-link spectrum identification item'] = linear_index
                CLSpecIdItemSet.add(specIdItem['cross-link spectrum identification item'])
                linear_index -= 1

        alternatives = []
        for id in CLSpecIdItemSet:
            CLSpecIdItemPair = [SpecIdItem for SpecIdItem in mzid_item['SpectrumIdentificationItem'] if
                                SpecIdItem['cross-link spectrum identification item'] == id]

            scores = {k: v for k, v in CLSpecIdItemPair[0].iteritems() if 'score' in k.lower() or 'pvalue' in k.lower() or 'evalue' in k.lower()}

            alternative = {
                "json_dict": mzid_to_json(CLSpecIdItemPair, mzid_reader),
                "passThreshold": CLSpecIdItemPair[0]['passThreshold'],
                "rank": CLSpecIdItemPair[0]['rank'],
                "scores": scores,
                "charge": CLSpecIdItemPair[0]['chargeState']
            }

            alternatives.append(alternative)

        # extract scanID
        try:
            scanID = int(mzid_item['peak list scans'])
        except KeyError:
            try:
                # ToDo: this might not work for all mzids. ProteomeDiscoverer 2.2 format 'scan=xx file=xx'
                matches = re.findall("([0-9]+)", mzid_item["spectrumID"])
                scanID = int(matches[0])
            except KeyError:
                returnJSON['errors'].append({"type": "mzidParseError", "message": "Error parsing scanID from mzidentml!"})
                continue

        # if premzml._offset_index.has_key(str(scanID)):
        #     scan = premzml.get_by_id(str(scanID))
        # elif premzml._offset_index.has_key('controllerType=0 controllerNumber=1 scan=' + str(scanID)):
        #     scan = premzml.get_by_id('controllerType=0 controllerNumber=1 scan=' + str(scanID))
        # else:
        #     returnJSON['errors'].append(
        #         {"type": "mzmlParseError", "message": "requested scanID %i not found in mzml file" % scanID})
        #     continue

        scan = pymzmlReader[scanID]

        if scan['ms level'] == 1:
            returnJSON['errors'].append(
                {"type": "mzmlParseError", "message": "requested scanID %i is not a MSn scan" % scanID})
            continue

        # peakList
        peaklist = get_peaklist_from_mzml(scan)

        for alt in alternatives:
            json_dict = alt['json_dict']
            json_dict['annotation']['mzid'] = mzid_item['id']
            json_dict['peaks'] = peaklist

            # ms2 tolerance ToDo: necessary? what to use as standard values?
            json_dict['annotation']['fragmentTolerance'] = {"tolerance": 20, "unit": "ppm"}

            # fragmentation ions
            json_dict['annotation']['ions'] = [{"type": "PeptideIon"}]

            frag_methods = {
                'beam-type collision-induced dissociation': ["BIon", "YIon"],
                'collision-induced dissociation': ["BIon", "YIon"],
                'electron transfer dissociation': ["CIon", "ZIon"],

            }

            # get fragMethod and translate that to Ion Types
            ion_types = []
            for key in scan.keys():
                if key in frag_methods.keys():
                    ion_types += frag_methods[key]

            ion_types = list(set(ion_types))
            for ion_type in ion_types:
                json_dict['annotation']['ions'].append({"type": ion_type})

            # extract other useful info to display
            rank = alt['rank']
            mzid = mzid_item['id']
            scores = json.dumps(alt['scores'])
            charge = alt['charge']

            # ToDo: handling for mzid that don't include isDecoy
            isDecoy = any([pep['isDecoy'] for pep in json_dict['annotation']['isDecoy']])
            accessions = ";".join(json_dict['annotation']['proteins'])

            #TODO: don't save them in json_dict in first place?
            del json_dict['annotation']['proteins']
            del json_dict['annotation']['isDecoy']

            try:
                rawFileName = mzid_item['spectraData_ref']
            except KeyError:
                returnJSON['errors'].append(
                    {"type": "mzidParseError", "message": "no spectraData_ref specified"})
                rawFileName = ""

            try:
                rawFileName = path_leaf(mzid_reader.get_by_id(rawFileName)['location'])
            except KeyError:
                pass

            # passThreshold
            if alt['passThreshold']:
                passThreshold = 1
            else:
                passThreshold = 0

            # peps and linkpos
            pep1 = "".join([x['aminoAcid'] + x['Modification'] for x in json_dict['Peptides'][0]['sequence']])
            if len(json_dict['Peptides']) > 1:
                pep2 = "".join([x['aminoAcid'] + x['Modification'] for x in json_dict['Peptides'][1]['sequence']])
                linkpos1 = [x['linkSite'] for x in json_dict['LinkSite'] if x['peptideId'] == 0][0] + 1
                linkpos2 = [x['linkSite'] for x in json_dict['LinkSite'] if x['peptideId'] == 1][0] + 1
            else:
                pep2 = ""
                linkpos1 = -1
                linkpos2 = -1

            # with con:
            #     cur.execute("INSERT INTO jsonReqs VALUES(%s, '%s', '%s', %s, %s)" % (
            #     specIdItem_index, json.dumps(json_dict), mzid, passThreshold, rank))

            multipleInjList_jsonReqs.append(
                [specIdItem_index,
                 json.dumps(json_dict),
                 mzid,
                 pep1,
                 pep2,
                 linkpos1,
                 linkpos2,
                 charge,
                 passThreshold,
                 rank,
                 scores,
                 isDecoy,
                 accessions,
                 rawFileName,
                 scanID]
            )
            specIdItem_index += 1

        mz_index += 1

        if specIdItem_index % 500 == 0:
            write_to_db(multipleInjList_jsonReqs, cur)
            multipleInjList_jsonReqs = []
            con.commit()
            if dev:
                break

    # once its done submit the last reqs to DB
    if len(multipleInjList_jsonReqs) > 0:
        write_to_db(multipleInjList_jsonReqs, cur)
        multipleInjList_jsonReqs = []
        con.commit()

    # delete uploaded files after they have been parsed
    if not dev:
        shutil.rmtree(upload_folder)

    if len(returnJSON["errors"]) > 0:
        returnJSON['response'] = "Warning: %i errors occured! See error log for more details." % len(returnJSON['errors'])
    else:
        returnJSON['response'] = "No errors! Smooth sailing."

    print json.dumps(returnJSON)
    if con:
        con.close()
        # print "end"

except Exception as e:
    logger.exception(e)


