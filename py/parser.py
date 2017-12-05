import pyteomics.mzid as py_mzid
import pyteomics.mgf as py_mgf
import pymzml
import re
import json
import sys
import sqlite3
import os
import shutil
import logging
import ntpath
import xmltodict


dev = False

if dev:
    logFile = "/home/lars/Xi/xiSPEC/log/parser.log"
else:
    logFile = "../log/" + sys.argv[3] + ".log"
    if not os.path.exists(logFile):
        open('file', 'w').close()


if not os.path.isfile(logFile):
    os.fdopen(os.open(logFile, os.O_WRONLY | os.O_CREAT, 0o777), 'w').close()
    # open(logFile, 'a').close()

logging.basicConfig(filename=logFile, level=logging.DEBUG,
                    format='%(asctime)s %(levelname)s %(name)s %(message)s')
logger = logging.getLogger(__name__)


def path_leaf(path):
    head, tail = ntpath.split(path)
    return tail or ntpath.basename(head)


def write_to_db(inj_list, cur):
    cur.executemany("""
INSERT INTO jsonReqs (
    'id',
    'annotation',
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
    'scanID',
    'peakList_id'
)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)""", inj_list)
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


# def get_peaklist_from_mzml(scan):
#     """
#     Function to extract peaklist in xiAnnotator JSON format dict from mzml
#
#     Parameters:
#     ------------------------
#     scan, pymzml reader spectrum
#     id: scanID
#     """
#
#     if "profile spectrum" in scan.keys():
#         peaks = scan.centroidedPeaks
#     else:
#         peaks = scan.centroidedPeaks
#
#     return [{"mz": peak[0], "intensity": peak[1]} for peak in peaks if peak[1] > 0]


def mzid_to_json(item, mzid_reader):
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

        # Target-Decoy
        peptideEvidences = [mzid_reader.get_by_id(s['peptideEvidence_ref']) for s in spectrumId_item['PeptideEvidenceRef']]
        # ToDo: isDecoy might not be defined. How to handle? (could make use of pyteomics.mzid.is_decoy())
        try:
            decoy = peptideEvidences[0]['isDecoy']
        except KeyError:
            decoy = None
        targetDecoy.append({"peptideId": pepIndex, 'isDecoy': decoy}) # TODO: multiple PeptideEvidenceRefs TD?

        # proteins
        proteins = [mzid_reader.get_by_id(p['dBSequence_ref']) for p in peptideEvidences]
        accessions = [p['accession'] for p in proteins]

        # convert pepsequence to dict
        pepId = spectrumId_item['peptide_ref']
        peptide = mzid_reader.get_by_id(pepId)
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

        # other parameters
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


def map_spectra_data_to_protocol(mzid_file):
    # extract and map spectrumIdentificationProtocol which includes annotation data like fragment tolerance
    with open(mzid_file) as fd:
        doc = xmltodict.parse(fd.read())

    map = {}

    SpectrumIdentifications = doc['MzIdentML']['AnalysisCollection']['SpectrumIdentification']
    if not type(SpectrumIdentifications) == list:
        SpectrumIdentifications = [SpectrumIdentifications]

    SpectrumIdentificationProtocols = doc['MzIdentML']['AnalysisProtocolCollection'][
        'SpectrumIdentificationProtocol']
    if not type(SpectrumIdentificationProtocols) == list:
        SpectrumIdentificationProtocols = [SpectrumIdentificationProtocols]

    SpectrumIdentificationProtocols_dict = {}
    for SpectrumIdentificationProtocol in SpectrumIdentificationProtocols:
        SpectrumIdentificationProtocols_dict[SpectrumIdentificationProtocol['@id']] = SpectrumIdentificationProtocol

    for SpectrumIdentification in SpectrumIdentifications:
        spectrumIdentificationProtocol_ref = SpectrumIdentification['@spectrumIdentificationProtocol_ref']
        spectraData_ref = SpectrumIdentification['InputSpectra']['@spectraData_ref']
        map[spectraData_ref] = {
            'protocol_ref': spectrumIdentificationProtocol_ref,
            'fragmentTolerance':
                SpectrumIdentificationProtocols_dict[spectrumIdentificationProtocol_ref]['FragmentTolerance'][
                    'cvParam']
        }

    return map


def get_ms2_tolerance(spectraData_ref):
    """
    Function to get ms2 tolerance for spectrumIdentification

    Parameters:
    ------------------------
    item: spectraData_ref - references the spectrumData
    """
    spectrumIdentificationProtocolInfo = spectraData_ProtocolMap[spectraData_ref]
    try:
        # ToDo check if forward and backward ppm error are the same
        ms2_tolerance = spectrumIdentificationProtocolInfo['fragmentTolerance'][0]
        value = re.search('([0-9.]+)', ms2_tolerance['@value']).groups()[0]
        if ms2_tolerance['@unitAccession'] == 'UO:0000169':
            unit = 'ppm'
        elif ms2_tolerance['@unitAccession'] == 'UO:0000221':
            unit = 'Da'
        else:
            unit = ms2_tolerance['@unitName']

    except (KeyError, AttributeError):
        value = '20.0'
        unit = 'ppm'
        returnJSON['errors'].append(
            {"type": "mzidParseError", "message": "could not parse ms2tolerance. Falling back to default values."})

    return value, unit


# print sys.argv[1]
# print sys.argv[2]
# print sys.argv[3]

if not dev:
    dbfolder = "../dbs/tmp/"
    try:
        os.stat(dbfolder)
    except:
        os.mkdir(dbfolder)

try:
    if dev:
        con = sqlite3.connect('../test.db')
    else:
        con = sqlite3.connect(dbfolder + sys.argv[3] + '.db')
    cur = con.cursor()
    cur.execute("DROP TABLE IF EXISTS jsonReqs")
    cur.execute(
        "CREATE TABLE jsonReqs("
            "id INT PRIMARY KEY, "
            "annotation TEXT, "
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
            "scanID INT, "
            "peakList_id INT)"
    )
    cur.execute("DROP TABLE IF EXISTS peakLists")
    cur.execute(
        "CREATE TABLE peakLists("
            "id INT PRIMARY KEY, "
            "peakList TEXT)"
    )
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
        baseDir = "/home/lars/work/xiSPEC/"
        mzidFile = baseDir + "DSSO_B170808_08_Lumos_LK_IN_90_HSA-DSSO-Sample_Xlink-CID-EThcD_CID-only.mzid"
        mzidFile = baseDir + 'PeptideShaker_mzid_1_2_example.mzid'
        peakList_file = baseDir + "centroid_B170808_08_Lumos_LK_IN_90_HSA-DSSO-Sample_Xlink-CID-EThcD.mzML"
        peakList_file = baseDir + "B170918_12_Lumos_LK_IN_90_HSA-DSSO-HCD_Rep1.mgf"

    else:
        mzidFile = sys.argv[1]
        peakList_file = sys.argv[2]
        upload_folder = "../../uploads/" + sys.argv[3]

    # https://raw.githubusercontent.com/HUPO-PSI/mzIdentML/master/schema/mzIdentML1.2.0.xsd
    mzidReader = py_mzid.MzIdentML(mzidFile)

    spectraData_ProtocolMap = map_spectra_data_to_protocol(mzidFile)

    # peakList file
    peakList_fileName = ntpath.basename(peakList_file).lower()

    if peakList_fileName.endswith('.mzml'):
        peakList_fileType = 'mzml'
        # premzml = mzml.PreIndexedMzML(mzml_file)
        pymzmlReader = pymzml.run.Reader(peakList_file)

    elif peakList_fileName.endswith('.mgf'):
        peakList_fileType = 'mgf'
        mgfReader = py_mgf.read(peakList_file)
        peakListArr = [pl for pl in mgfReader]

    mzidItem_index = 0
    specIdItem_index = 0
    multipleInjList_jsonReqs = []
    multipleInjList_peakLists = []

    # main loop
    # mzid_item = mzid_reader.next()
    for mzid_item in mzidReader:
        # find id pairs of cross-linked items
        SpecIdSet = set()
        linear_index = -1   # negative index values for linear peptides

        for specIdItem in mzid_item['SpectrumIdentificationItem']:
            if 'cross-link spectrum identification item' in specIdItem.keys():
                SpecIdSet.add(specIdItem['cross-link spectrum identification item'])
            else:  # assuming linear
                # misusing 'cross-link spectrum identification item' for linear peptides with negative index
                specIdItem['cross-link spectrum identification item'] = linear_index
                SpecIdSet.add(specIdItem['cross-link spectrum identification item'])
                linear_index -= 1

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

        # peakList
        if peakList_fileType == 'mzml':
            try:
                scan = pymzmlReader[scanID]
            except KeyError:
                returnJSON['errors'].append(
                    {"type": "mzmlParseError", "message": "requested scanID %i not found in peakList file" % scanID})
                continue
            if scan['ms level'] == 1:
                returnJSON['errors'].append(
                    {"type": "mzmlParseError", "message": "requested scanID %i is not a MSn scan" % scanID})
                continue

            peakList = "\n".join(["%s %s" % (mz, i) for mz, i in scan.peaks if i > 0])
            # peaklist = get_peaklist_from_mzml(scan)

        elif peakList_fileType == 'mgf':
            try:
                scan = peakListArr[scanID]
            except IndexError:
                returnJSON['errors'].append(
                    {"type": "mzmlParseError", "message": "requested scanID %i not found in peakList file" % scanID})
                continue
            peaks = zip(scan['m/z array'], scan['intensity array'])
            peakList = "\n".join(["%s %s" % (mz, i) for mz, i in peaks if i > 0])

        multipleInjList_peakLists.append([mzidItem_index, peakList])

        ms2TolValue, ms2TolUnit = get_ms2_tolerance(mzid_item['spectraData_ref'])

        # alternatives = []
        for id in SpecIdSet:
            paired_specIdItem = [sid_item for sid_item in mzid_item['SpectrumIdentificationItem'] if
                                 sid_item['cross-link spectrum identification item'] == id]

            scores = {k: v for k, v in paired_specIdItem[0].iteritems() if
                      'score' in k.lower() or 'pvalue' in k.lower() or 'evalue' in k.lower()}

            alternative = {
                "json_dict": mzid_to_json(paired_specIdItem, mzidReader),
                "passThreshold": paired_specIdItem[0]['passThreshold'],
                "rank": paired_specIdItem[0]['rank'],
                "scores": scores,
                "charge": paired_specIdItem[0]['chargeState']
            }

            json_dict = alternative['json_dict']
            json_dict['annotation']['mzid'] = mzid_item['id']
            json_dict['annotation']['fragmentTolerance'] = {"tolerance": ms2TolValue, "unit": ms2TolUnit}

            # fragmentation ions
            json_dict['annotation']['ions'] = [{"type": "PeptideIon"}]

            try:
                ionsList = paired_specIdItem[0]['IonType']
                ionNamesList = [i['name'] for i in paired_specIdItem[0]['IonType']]
                ionNamesList = list(set(ionNamesList))
            except KeyError:
                ionNamesList = []

            ion_types = []
            for ionName in ionNamesList:
                try:
                    ion = re.search('frag: ([a-z]) ion', ionName).groups()[0]
                    ion_types.append(ion.upper() + 'Ion')
                except (IndexError, AttributeError) as e:
                    #print ionName
                    continue

            # if no ion types are specified in the id file check the mzML file
            if len(ion_types) == 0 and peakList_fileType == 'mzml':

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
            rank = alternative['rank']
            mzid = mzid_item['id']
            scores = json.dumps(alternative['scores'])
            charge = alternative['charge']

            # ToDo: handling for mzid that don't include isDecoy
            isDecoy = any([pep['isDecoy'] for pep in json_dict['annotation']['isDecoy']])
            accessions = ";".join(json_dict['annotation']['proteins'])

            #TODO: don't save them in json_dict in first place?
            del json_dict['annotation']['proteins']
            del json_dict['annotation']['isDecoy']

            # raw file name
            try:
                rawFileName = mzid_item['spectraData_ref']
            except KeyError:
                returnJSON['errors'].append(
                    {"type": "mzidParseError", "message": "no spectraData_ref specified"})
                rawFileName = ""

            try:
                rawFileName = path_leaf(mzidReader.get_by_id(rawFileName)['location'])
            except KeyError:
                pass

            # passThreshold
            if alternative['passThreshold']:
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

            multipleInjList_jsonReqs.append(
                 [specIdItem_index,
                 json.dumps(json_dict['annotation']),
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
                 scanID,
                 mzidItem_index]
            )
            specIdItem_index += 1

        mzidItem_index += 1

        if specIdItem_index % 500 == 0:
            write_to_db(multipleInjList_jsonReqs, cur)
            multipleInjList_jsonReqs = []

            cur.executemany("""INSERT INTO peakLists ('id', 'peaklist') VALUES (?, ?)""", multipleInjList_peakLists)
            multipleInjList_peakLists = []

            con.commit()
            if dev:
                break

    # once its done submit the last reqs to DB
    if len(multipleInjList_jsonReqs) > 0:
        write_to_db(multipleInjList_jsonReqs, cur)
        multipleInjList_jsonReqs = []

        cur.executemany("""INSERT INTO peakLists ('id', 'peaklist') VALUES (?, ?)""", multipleInjList_peakLists)
        multipleInjList_peakLists = []

        con.commit()

    # delete uploaded files after they have been parsed
    if not dev:
        shutil.rmtree(upload_folder)

    if len(returnJSON["errors"]) > 0:
        returnJSON['response'] = "Warning: %i errors occured! See error log for more details." % len(returnJSON['errors'])
        for e in returnJSON['errors']:
            logger.error(e)
    else:
        returnJSON['response'] = "No errors! Smooth sailing."

    print json.dumps(returnJSON)
    if con:
        con.close()
        # print "end"

except Exception as e:
    logger.exception(e)
    returnJSON['errors'].append(
        {"type": "Error", "message": e})


