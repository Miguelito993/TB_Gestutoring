/*
    Travail de Bachelor 2017 - GesTutoring
    Auteur: Miguel Pereira Vieira
    Date: 12.07.2017
    Lieu: Genève
    Version: 1.0

    Serveur NodeJS
*/
const HOSTNAME = '127.0.0.1';
const PORT = 4242;

const HOST_DB = 'localhost'
const PORT_DB = 27017;
const NAME_DB = 'db_gestutoring';

var urlDB = 'mongodb://' + HOST_DB + ':' + PORT_DB + '/' + NAME_DB;
var waitingUsers = {};
var initialTime;

var app = require('express')(),
  multer = require('multer'),
  server = require('http').createServer(app),
  io = require('socket.io').listen(server),
  ent = require('ent'), // Permet de bloquer les caractères HTML (sécurité équivalente à htmlentities en PHP)
  morgan = require('morgan'), // Utile pour les logs
  MongoClient = require('mongodb').MongoClient, // Accès à la base de données
  ObjectId = require('mongodb').ObjectId,
  cors = require('cors'), // Autorise les requêtes cross-domain
  assert = require('assert'),
  util = require('util'), // Afficher le contenu des objets
  bodyParser = require('body-parser'), // Permet de récupérer les paramètres d'une requête POST
  ExpressPeerServer = require('peer').ExpressPeerServer;


var storage = multer.diskStorage({
    destination: function (req, file, callback) {
        if (file.mimetype == 'application/pdf') {
            callback(null, './uploads');
        } else if (file.mimetype.split('/')[0] == 'image') {
            callback(null, './img');
        }
    },
    filename: function (req, file, callback) {
        var user = parseUserInfo(req.body, file.mimetype.split('/'));

        if (file.mimetype == 'application/pdf') {
            MongoClient.connect(urlDB, function (err, db) {
                assert.equal(err, null);

                // Impose un timer dans le cas où l'utilisateur charge plusieurs diplômes à faire valider
                setTimeout(checkLogin(db, {pseudo: user.pseudo, password: user.pwd}, function (docs) {
                    if (docs[0] == null) {
                        // Ajouter le nouvel utilisateur avec le nouveau diplome
                        insertUser(db, user);
                    } else {
                        // Insérer le diplôme avec l'identifiant de l'utilisateur récupéré
                        user.id = docs[0]._id;
                        addDiplome(db, user);
                    }
                    db.close();
                }), 1000);
            });

            callback(null, user.diplomes);
        } else if (file.mimetype.split('/')[0] == 'image') {
            MongoClient.connect(urlDB, function (err, db) {
                assert.equal(err, null);

                setTimeout(checkLogin(db, {pseudo: user.pseudo, password: user.pwd}, function (docs) {
                    if (docs[0] == null) {
                        // Ajouter le nouvel utilisateur avec son image de profil
                        insertUser(db, user);
                    } else {
                        // Insérer l'image avec l'identifiant de l'utilisateur récupéré
                        user.id = docs[0]._id;
                        addImageProfile(db, user);
                    }
                    db.close();
                }), 1000);
            });
            callback(null, user.imgProfil);
        }
    }
});

var storageDatas = multer.diskStorage({
    destination: function (req, file, callback) {
        callback(null, './datas');
    },
    filename: function (req, file, callback) {
        var datas = parseDatasInfo(req.body);

        MongoClient.connect(urlDB, function (err, db) {
            assert.equal(err, null);
            insertDatas(db, datas);
            db.close();
        });
        callback(null, datas.fichier);
    }
});

var upload = multer({storage: storage}).any();
var uploadDatas = multer({storage: storageDatas}).any();

function parseUserInfo(body, myFile) {
    var fileStatus = (myFile != null);
    var user = {
        id: body.id,
        firstname: ent.encode(body.inputFirstname),
        name: ent.encode(body.inputName),
        email: ent.encode(body.inputEmail),
        pseudo: ent.encode(body.inputPseudo),
        pwd: ent.encode(body.inputPassword),
        city: body.inputCity,
        soldes: 0,
        type: body.inputType,
        isOnline: false,
        imgProfil: (fileStatus) ? ((myFile[0] == 'image') ? ent.encode(body.inputPseudo) + '.' + myFile[1] : null) : null,
        emailParent: (body.inputType === "Student") ? ent.encode(body.inputEmailParent) : null,
        diplomes: (body.inputType === "Coach") ? 'dipl_' + ent.encode(body.inputPseudo) + '_' + Date.now() + '.pdf' : null,
        tarif: (body.inputType === "Coach") ? ent.encode(body.inputTarif) : null,
        isValid: (body.inputType === "Coach") ? false : true,
        matieres: (body.inputType === "Coach") ? body.inputMatiere : null
    }
    return user;
}

function parseDatasInfo(body) {
    var datas = {
        typeData: body.ad_inputType,
        fichier: body.ad_inputType + '_' + Date.now() + '.pdf',
        keywords: body.ad_inputKeyword.split(';'),
        access: body.ad_Access,
        idMatiere: body.ad_inputMatiere,
        idUser: body.ad_inputUser
    }
    return datas;
}

function doAverageNotation(tabNota) {
    var notation = 0
    for (var i = 0; i < tabNota.length; i++) {
        notation += tabNota[i].notation;
    }
    return (notation /= tabNota.length);
}



var getDepartments = function (db, callback) {
    var collection = db.collection('t_departments');

    collection.find({}).toArray(
      function (err, docs) {
          assert.equal(err, null);
          callback(docs);
      });
}

var getMatieres = function (db, matiere, callback) {
    var collection = db.collection('t_matieres');

    if (matiere === null) {
        collection.find({}, {name: 1}).toArray(
          function (err, docs) {
              assert.equal(err, null);
              callback(docs);
          });
    } else {
        collection.find({name: matiere}).toArray(
          function (err, docs) {
              assert.equal(err, null);
              callback(docs);
          });
    }
}

var checkLogin = function (db, user, callback) {
    var collection = db.collection('t_users');

    collection.find({pseudo: user.pseudo, password: user.password}).toArray(
      function (err, docs) {
          assert.equal(err, null);
          callback(docs);
      });
}

var changeStatus = function (db, info, callback) {
    var collection = db.collection('t_users');

    collection.update({_id: ObjectId(info.id)}, {$set: {isOnline: (info.statusOnline == 'true')}},
      function (err, docs) {
          assert.equal(err, null);
          callback(docs);
      }
    );
}

var getCoaches = function (db, matiere, callback) {
    var collection = db.collection('t_users');

    if (matiere != 'null') {
        collection.find({type: "Coach", isValid: true, matieres: matiere}).sort({tarif: 1}).toArray(
          function (err, docs) {
              assert.equal(err, null);
              callback(docs);
          }
        );
    } else {
        collection.find({type: "Coach", isValid: true}).sort({tarif: 1}).toArray(
          function (err, docs) {
              assert.equal(err, null);
              callback(docs);
          }
        );
    }
}

var getNotation = function (db, info, callback) {
    var collection = db.collection('t_notations');

    collection.find({id_coach: ObjectId(info.id)}, {notation: 1}).toArray(
      function (err, docs) {
          assert.equal(err, null);
          callback(docs);
      }
    );
}

var getPlanning = function (db, info, callback) {
    var collection = db.collection('t_meetings');

    collection.aggregate([
        {$match: {id_coach: ObjectId(info.id_user)}},
        {$match: {date: {$lt: new Date(info.dateLimit), $gt: new Date(info.dateNow)}}},
        {$sort: {date: 1}}
    ]).toArray(
      function (err, docs) {
          assert.equal(err, null);
          callback(docs);
      }
    );
}

var getAllPlanning = function (db, info, callback) {
    var collection = db.collection('t_meetings');

    if (info.type == 'Coach') {
        collection.aggregate([
            {$match: {id_coach: ObjectId(info.id_user)}},
            {$match: {date: {$gt: new Date(info.dateNow)}}},
            {$sort: {date: 1}}
        ]).toArray(
          function (err, docs) {
              assert.equal(err, null);
              callback(docs);
          }
        );
    } else {
        collection.aggregate([
            {$match: {id_student: ObjectId(info.id_user)}},
            {$match: {date: {$gt: new Date(info.dateNow)}}},
            {$sort: {date: 1}}
        ]).toArray(
          function (err, docs) {
              assert.equal(err, null);
              callback(docs);
          }
        );
    }
}

var submitMeeting = function (db, info, callback) {
    var collection = db.collection('t_meetings');

    collection.insertOne({
        date: new Date(info.date),
        isFree: (info.isFree == 'true') ? true : false,
        duration: parseInt(info.duration),
        isEnded: (info.isEnded == 'true') ? true : false,
        id_coach: ObjectId(info.id_coach),
        id_student: (info.id_student == '') ? null : ObjectId(info.id_student),
        id_matiere: (info.id_matiere == '') ? null : ObjectId(info.id_matiere)
    }, function (err, docs) {
        assert.equal(err, null);
        callback(docs);
    });
}

var getDataList = function (db, info, callback) {
    var collection = db.collection('t_datas');

    collection.find({id_matiere: ObjectId(info.idMatiere)}).toArray(
      function (err, docs) {
          assert.equal(err, null);
          callback(docs);
      }
    );
}

var getIdByPseudo = function (db, info, callback) {
    var collection = db.collection('t_users');

    collection.find({pseudo: info}, {_id: 1}).toArray(
      function (err, docs) {
          assert.equal(err, null);
          callback(docs);
      }
    );
}

var getPseudoById = function (db, info, callback) {
    var collection = db.collection('t_users');

    if (info != null) {
        collection.find({_id: ObjectId(info)}, {pseudo: 1}).toArray(
          function (err, docs) {
              assert.equal(err, null);
              callback(docs);
          }
        );
    }
}

var getUserById = function (db, info, callback) {
    var collection = db.collection('t_users');

    if (info != null) {
        collection.find({_id: ObjectId(info)}).toArray(
          function (err, docs) {
              assert.equal(err, null);
              callback(docs);
          }
        );
    }
}

var getNamesById = function (db, info, callback) {
    var collection = db.collection('t_users');

    if (info != null) {
        collection.find({_id: ObjectId(info)}, {prenom: 1, nom: 1}).toArray(
          function (err, docs) {
              assert.equal(err, null);
              callback(docs);
          }
        );
    }
}

var getMeeting = function (db, info, callback) {
    var collection = db.collection('t_meetings');

    // Pour les répétiteurs
    if (info.type == 'Coach') {
        collection.find({isFree: false, isEnded: false, id_coach: ObjectId(info.myID)}).toArray(
          function (err, docs) {
              assert.equal(err, null);
              callback(docs);
          }
        );
    } else {  // Pour les étudiants
        collection.find({isFree: false, isEnded: false, id_student: ObjectId(info.myID)}).toArray(
          function (err, docs) {
              assert.equal(err, null);
              callback(docs);
          }
        );
    }
}

var getMatiereByID = function (db, info, callback) {
    var collection = db.collection('t_matieres');

    if (info != null) {
        collection.find({_id: ObjectId(info)}, {name: 1}).toArray(
          function (err, docs) {
              assert.equal(err, null);
              callback(docs);
          }
        );
    }
}

var submitNotation = function (db, info, callback) {
    var collection = db.collection('t_notations');

    collection.insertOne({
        notation: parseInt(info.note),
        description: ent.encode(info.comment),
        id_coach: ObjectId(info.id_coach)
    }, function (err, docs) {
        assert.equal(err, null);
        callback(docs);
    });
}

var doTransaction = function (db, info) {
    var collection = db.collection('t_users');

    // Ajouter sur le compte du répétiteur
    collection.update({_id: ObjectId(info.idCoach)}, {$inc: {soldes: parseFloat(info.somme)}},
      function (err) {
          assert.equal(err, null);
      }
    );

    // Retirer du compte de l'étudiant
    collection.update({_id: ObjectId(info.idStudent)}, {$inc: {soldes: -parseFloat(info.somme)}},
      function (err) {
          assert.equal(err, null);
      }
    );
}

var endingSession = function (db, id) {
    var collection = db.collection('t_meetings');

    collection.update({_id: ObjectId(id)}, {$set: {isEnded: true}},
      function (err) {
          assert.equal(err, null);
      }
    );
}

var getMatiereIDByName = function (db, info, callback) {
    var collection = db.collection('t_matieres');

    if (info != null) {
        collection.find({name: info}).toArray(
          function (err, docs) {
              assert.equal(err, null);
              callback(docs);
          }
        );
    }
}

var makeMeeting = function (db, info, callback) {
    var collection = db.collection('t_meetings');

    collection.update({_id: ObjectId(info.idMeeting)}, {$set: {isFree: false, id_student: ObjectId(info.idStudent), id_matiere: ObjectId(info.idMatiere)}},
      function (err, docs) {
          assert.equal(err, null);
          callback(docs);
      }
    );
}

var modifyProfile = function (db, info, callback) {
    var collection = db.collection('t_users');

    // Pour les répétiteurs
    if (info.type == 'Coach') {
        collection.update({_id: ObjectId(info.id)}, {$set: {prenom: ent.encode(info.firstname), nom: ent.encode(info.name), email: ent.encode(info.email), password: ent.encode(info.pwd), canton: info.city, tarif: ent.encode(info.tarif), matieres: info.matiere}},
          function (err, docs) {
              assert.equal(err, null);
              callback(docs);
          }
        );
    } else if (info.type == 'Student') {  // Pour les étudiants
        collection.update({_id: ObjectId(info.id)}, {$set: {prenom: ent.encode(info.firstname), nom: ent.encode(info.name), email: ent.encode(info.email), password: ent.encode(info.pwd), canton: info.city, emailParent: ent.encode(info.emailParent)}},
          function (err, docs) {
              assert.equal(err, null);
              callback(docs);
          }
        );
    }
}

var deleteDiplome = function (db, info, callback) {
    var collection = db.collection('t_users');

    collection.update({_id: ObjectId(info.id)}, {$pull: {diplomes: info.diplome}},
      function (err, docs) {
          assert.equal(err, null);
          callback(docs);
      }
    );
}

var getEndedMeeting = function (db, info, callback) {
    var collection = db.collection('t_meetings');

    // Pour les répétiteurs
    if (info.type == 'Coach') {
        collection.find({isFree: false, isEnded: true, id_coach: ObjectId(info.myID)}).toArray(
          function (err, docs) {
              assert.equal(err, null);
              callback(docs);
          }
        );
    } else {  // Pour les étudiants
        collection.find({isFree: false, isEnded: true, id_student: ObjectId(info.myID)}).toArray(
          function (err, docs) {
              assert.equal(err, null);
              callback(docs);
          }
        );
    }
}

var getUserInvalid = function (db, callback) {
    var collection = db.collection('t_users');

    collection.find({isValid: false}).toArray(
      function (err, docs) {
          assert.equal(err, null);
          callback(docs);
      });

}

var validUser = function (db, info, callback) {
    var collection = db.collection('t_users');

    collection.update({_id: ObjectId(info.id)}, {$set: {isValid: ('true' == 'true')}},
      function (err, docs) {
          assert.equal(err, null);
          callback(docs);
      }
    );
}

var insertDatas = function (db, datas) {
    var collection = db.collection('t_datas');

    collection.insertOne({
        typeData: datas.typeData,
        data: datas.fichier,
        keywords: datas.keywords,
        access: datas.access,
        id_user: ObjectId(datas.idUser),
        id_matiere: ObjectId(datas.idMatiere)
    }, function (err) {
        assert.equal(err, null);
    });
}

var insertUser = function (db, user) {
    var collection = db.collection('t_users');

    collection.insertOne({
        prenom: user.firstname,
        nom: user.name,
        email: user.email,
        pseudo: user.pseudo,
        password: user.pwd,
        canton: user.city,
        soldes: user.soldes,
        type: user.type,
        emailParent: user.emailParent,
        diplomes: [user.diplomes],
        tarif: user.tarif,
        img_profil: user.imgProfil,
        isValid: user.isValid,
        isOnline: user.isOnline,
        matieres: user.matieres
    }, function (err) {
        assert.equal(err, null);
    });
}

var addDiplome = function (db, user) {
    var collection = db.collection('t_users');

    collection.update({"_id": ObjectId(user.id)}, {$push: {"diplomes": user.diplomes}},
      function (err) {
          assert.equal(err, null);
      }
    );
}

var addImageProfile = function (db, user) {
    var collection = db.collection('t_users');

    collection.update({"_id": ObjectId(user.id)}, {$set: {"img_profil": user.imgProfil}},
      function (err) {
          assert.equal(err, null);
      }
    );
}

app.use(morgan('combined')); // Log dans la console

app.use(cors()); // Autorise toutes les CORS Requests

app.use(bodyParser.json()); // Supporte encodage json body
app.use(bodyParser.urlencoded({extended: true})); // Supporte encodage body

app.get('/getDepartments', function (req, res) {
    res.setHeader('Content-Type', 'text/plain');

    MongoClient.connect(urlDB, function (err, db) {
        assert.equal(err, null);

        getDepartments(db, function (docs) {
            res.jsonp(docs);
            db.close();
        });
    });
});

app.get('/getMatieres', function (req, res) {
    res.setHeader('Content-Type', 'text/plain');

    MongoClient.connect(urlDB, function (err, db) {
        assert.equal(err, null);

        getMatieres(db, null, function (docs) {
            res.jsonp(docs);
            db.close();
        });
    });
});

app.post('/checkLogin', function (req, res) {
    var user = {"pseudo": req.body.pseudo, "password": req.body.pwd};

    MongoClient.connect(urlDB, function (err, db) {
        assert.equal(err, null);

        checkLogin(db, user, function (docs) {
            if (docs[0] != null) {
                if (docs[0].isValid == true) {
                    res.jsonp({status: 'Success', docs});
                } else {
                    res.jsonp({status: 'NotValid'});
                }
            } else {
                res.jsonp({status: 'Failed'});
            }

            db.close();
        });
    });
});

app.post('/changeStatus', function (req, res) {
    var info = {"id": req.body.id, "statusOnline": req.body.statusOnline};

    if ((info.id != null) && (info.statusOnline != null)) {
        MongoClient.connect(urlDB, function (err, db) {
            assert.equal(err, null);

            changeStatus(db, info, function (docs) {
                res.jsonp(docs);
                db.close();
            });
        });
    }
});

app.post('/submitInscription', function (req, res) {
    upload(req, res, function (err) {
        if (err) {
            return res.send(err);
        }
        res.send("Inscription is done");
    });
});

app.post('/submitDatas', function (req, res) {
    uploadDatas(req, res, function (err) {
        if (err) {
            return res.send(err);
        }
        res.send("Additionnal data is inserted");
    });
});

app.get('/getCoaches/:matiere', function (req, res) {
    res.setHeader('Content-Type', 'text/plain');

    var param = req.params.matiere;

    MongoClient.connect(urlDB, function (err, db) {
        assert.equal(err, null);

        getCoaches(db, param, function (docs) {
            res.jsonp(docs);
            db.close();
        });
    });
});

app.post('/getNotation', function (req, res) {
    var info = {"id": req.body.id_coach};

    if (info.id != null) {
        MongoClient.connect(urlDB, function (err, db) {
            assert.equal(err, null);

            getNotation(db, info, function (docs) {
                res.jsonp(doAverageNotation(docs));
                db.close();
            });
        });
    }
});

app.post('/getPlanning', function (req, res) {
    var info = {"id_user": req.body.id_user, "dateLimit": req.body.dateLimit, "dateNow": req.body.dateNow, "type": req.body.type};

    if (info.id_user != null) {
        MongoClient.connect(urlDB, function (err, db) {
            assert.equal(err, null);

            if (info.dateLimit != null) {
                getPlanning(db, info, function (docs) {
                    res.jsonp(docs);
                    db.close();
                });
            } else {
                getAllPlanning(db, info, function (docs) {
                    res.jsonp(docs);
                    db.close();
                });
            }
        });
    }
});

app.post('/submitMeeting', function (req, res) {
    var info = {"id_coach": req.body.id_coach, "date": req.body.date, "isFree": req.body.isFree, "duration": req.body.duration, "isEnded": req.body.isEnded, "id_student": req.body.id_student, "id_matiere": req.body.id_matiere};

    if ((info.id_coach != null) && (info.date != null)) {
        MongoClient.connect(urlDB, function (err, db) {
            assert.equal(err, null);

            submitMeeting(db, info, function (docs) {
                res.jsonp(docs);
                db.close();
            });
        });
    }
});

app.get('/getDataList/:matiere/:idCoach', function (req, res) {
    var param = req.params.matiere;
    var info = {idCoach: req.params.idCoach};
    var dataList = [];

    MongoClient.connect(urlDB, function (err, db) {
        assert.equal(err, null);

        getMatieres(db, param, function (docs) {
            info.idMatiere = docs[0]._id;
            getDataList(db, info, function (result) {
                for (var i = 0; i < result.length; i++) {
                    if (result[i].access === 'public') {
                        dataList.push(result[i]);
                    } else {                        
                        if (result[i].id_user.toString() === info.idCoach) {                            
                            dataList.push(result[i]);
                        }
                    }
                }

                res.jsonp(dataList);
                db.close();
            });
        });
    });
});

app.get('/getIdByPseudo/:pseudo', function (req, res) {
    var param = req.params.pseudo;

    MongoClient.connect(urlDB, function (err, db) {
        assert.equal(err, null);

        getIdByPseudo(db, param, function (docs) {
            res.jsonp(docs);
            db.close();
        });
    });
});

app.get('/getPseudoById/:idUser', function (req, res) {
    var info = req.params.idUser;

    if (info !== 'null') {
        MongoClient.connect(urlDB, function (err, db) {
            assert.equal(err, null);

            getPseudoById(db, info, function (docs) {
                res.jsonp(docs);
                db.close();
            });
        });
    } else {
        res.send(null);
    }
});

app.get('/getUserById/:idUser', function (req, res) {
    var info = req.params.idUser;

    if (info !== 'null') {
        MongoClient.connect(urlDB, function (err, db) {
            assert.equal(err, null);

            getUserById(db, info, function (docs) {
                res.jsonp(docs);
                db.close();
            });
        });
    } else {
        res.send(null);
    }
});

app.get('/getNamesById/:idUser', function (req, res) {
    var info = req.params.idUser;

    if (info !== 'null') {
        MongoClient.connect(urlDB, function (err, db) {
            assert.equal(err, null);

            getNamesById(db, info, function (docs) {
                res.jsonp(docs);
                db.close();
            });
        });
    } else {
        res.send(null);
    }
});


app.post('/getMeeting', function (req, res) {
    var info = {"type": req.body.type, "myID": req.body.myID};

    if ((info.type != null) && (info.myID != null)) {
        MongoClient.connect(urlDB, function (err, db) {
            assert.equal(err, null);

            getMeeting(db, info, function (docs) {
                res.jsonp(docs);
                db.close();
            });
        });
    }
});

app.get('/getMatiereByID/:idMatiere', function (req, res) {
    var param = req.params.idMatiere;

    MongoClient.connect(urlDB, function (err, db) {
        assert.equal(err, null);

        getMatiereByID(db, param, function (docs) {
            res.jsonp(docs);
            db.close();
        });
    });
});

app.post('/submitNotation', function (req, res) {
    var info = {"note": req.body.note, "comment": req.body.comment, "id_coach": req.body.id_coach};

    if ((info.note != null) && (info.id_coach != null)) {
        MongoClient.connect(urlDB, function (err, db) {
            assert.equal(err, null);

            submitNotation(db, info, function (docs) {
                res.jsonp(docs);
                db.close();
            });
        });
    }
});

app.get('/getMatiereIDByName/:matiere', function (req, res) {
    var param = req.params.matiere;

    MongoClient.connect(urlDB, function (err, db) {
        assert.equal(err, null);

        getMatiereIDByName(db, param, function (docs) {
            res.jsonp(docs);
            db.close();
        });
    });
});

app.post('/makeMeeting', function (req, res) {
    var info = {"idMeeting": req.body.idMeeting, "idStudent": req.body.idStudent, "idMatiere": req.body.idMatiere};

    if ((info.idMeeting != null) && (info.idStudent != null) && (info.idMatiere != null)) {
        MongoClient.connect(urlDB, function (err, db) {
            assert.equal(err, null);

            makeMeeting(db, info, function (docs) {
                res.jsonp(docs);
                db.close();
            });
        });
    }
});

app.get('/getFile/:folder/:name', function (req, res) {
    var fileName = req.params.name;
    var typeFile = req.params.folder;

    var options = {
        root: __dirname + '/' + typeFile + '/',
        dotfiles: 'deny',
        headers: {
            'x-timestamp': Date.now(),
            'x-sent': true
        }
    };

    var fileName = req.params.name;
    var access = true

    if (access) {
        res.sendFile(fileName, options, function (err) {
            assert.equal(err, null);
        });
    } else {
        res.status(400);
        res.send('None shall pass');
    }

});

app.post('/modifyProfile', function (req, res) {
    var info = {"id": req.body.id,
        "firstname": req.body.firstname,
        "name": req.body.name,
        "email": req.body.email,
        "pwd": req.body.pwd,
        "city": req.body.city,
        "type": req.body.type,
        "tarif": req.body.tarif,
        "matiere": req.body.matiere,
        "emailParent": req.body.emailParent};

    if ((info.id != null) &&
      (info.firstname != null) &&
      (info.name != null) &&
      (info.email != null) &&
      (info.pwd != null) &&
      (info.city != null) &&
      (info.type != null)) {
        MongoClient.connect(urlDB, function (err, db) {
            assert.equal(err, null);

            modifyProfile(db, info, function (docs) {
                res.jsonp(docs);
                db.close();
            });
        });
    }
});

app.post('/deleteDiplome', function (req, res) {
    var info = {"id": req.body.id, "diplome": req.body.diplome};

    if ((info.id != null) && (info.diplome != null)) {
        MongoClient.connect(urlDB, function (err, db) {
            assert.equal(err, null);

            deleteDiplome(db, info, function (docs) {
                res.jsonp(docs);
                db.close();
            });
        });
    }
});

app.post('/getEndedMeeting', function (req, res) {
    var info = {"type": req.body.type, "myID": req.body.myID};

    if ((info.type != null) && (info.myID != null)) {
        MongoClient.connect(urlDB, function (err, db) {
            assert.equal(err, null);

            getEndedMeeting(db, info, function (docs) {
                res.jsonp(docs);
                db.close();
            });
        });
    }
});

app.get('/getUserInvalid', function (req, res) {
    MongoClient.connect(urlDB, function (err, db) {
        assert.equal(err, null);

        getUserInvalid(db, function (docs) {
            res.jsonp(docs);
            db.close();
        });
    });
});

app.post('/validUser', function (req, res) {
    var info = {"id": req.body.id};

    if (info.id != null) {
        MongoClient.connect(urlDB, function (err, db) {
            assert.equal(err, null);

            validUser(db, info, function (docs) {
                res.jsonp(docs);
                db.close();
            });
        });
    }
});

app.use('/peerjs', ExpressPeerServer(server, {debug: true}));

app.use(function (req, res, next) {
    res.setHeader('Content-Type', 'text/plain');
    res.status(404).send('Page introuvable !\n');
});

io.sockets.on('connection', function (socket) {
    socket.on('nouveau_client', function (userInfo) {
        socket.pseudo = userInfo.pseudo;
        waitingUsers[socket.pseudo] = {};
        waitingUsers[socket.pseudo]['myID'] = userInfo.myID;
        waitingUsers[socket.pseudo]['type'] = userInfo.type;
        if (userInfo.type == 'Coach') {
            waitingUsers[socket.pseudo]['tarif'] = userInfo.tarif;
        }
        waitingUsers[socket.pseudo]['myPartner'] = userInfo.myPartner;

        if (waitingUsers[userInfo.myPartner] != null) {
            setTimeout(function () {
                socket.emit('find_partner', {partnerID: waitingUsers[userInfo.myPartner]['myID'], partnerName: userInfo.myPartner});
                initialTime = new Date().getTime();
            }, 10000);
        }
    });

    socket.on('close_socket', function (info) {
        if (waitingUsers[info.myPseudo]['type'] == 'Coach') {
            var d = new Date().getTime();
            var durationTime = Math.round(((d - initialTime) / 1000) * 100) / 100;  // Arrondi à deux décimales

            // Le temps est en secondes donc / 3600
            var somme = Math.round(((waitingUsers[info.myPseudo]['tarif'] * durationTime) / 3600) * 100) / 100;   // Arrondi à deux décimales

            // Effectue la transaction entre le compte de l'étudiant et du répétiteur
            var infoDB = {"idCoach": waitingUsers[info.myPseudo]['myID'], "somme": somme, "idStudent": waitingUsers[info.partnerPseudo]['myID']};
            MongoClient.connect(urlDB, function (err, db) {
                assert.equal(err, null);

                doTransaction(db, infoDB);
                db.close();
            });

            delete waitingUsers[info.myPseudo];
            delete waitingUsers[info.partnerPseudo];
        }
    });

    socket.on('close_session', function (info) {
        var idSession = info.idSession;

        MongoClient.connect(urlDB, function (err, db) {
            assert.equal(err, null);

            endingSession(db, idSession);
            db.close();
        });
    });
});

server.listen(PORT, HOSTNAME, () => {
    console.log('Serveur: http://' + HOSTNAME + ':' + PORT);
});