import sys
import pymongo

from pymongo import MongoClient
client = MongoClient()

def usage(argv0):
    print("usage:", argv0, "<nom_fichier> <nom_base_de_donnees>")
    sys.exit(1)

def main(argv=sys.argv):
    if len(argv) < 3 or len(argv) > 4:
        usage(argv[0])

    # Open File
    nameFile = argv[1]
    file = open(nameFile, "r")

    nameBDD = argv[2]

    # Connect database and select collection
    client = MongoClient('mongodb://localhost:27017/')
    db = client['db_gestutoring']
    collection = db[nameBDD]

    # Split all lines of file into an array
    allLines = file.read().splitlines()

    for value in allLines:
        post = {
            "name": value
        }

        # Insert our variable post on collection
        collection.insert_one(post)

        print(post)

    file.close()

if __name__ == "__main__":
    main()