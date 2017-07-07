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
    bodyParser = require('body-parser'),
    path = require('path'),
    fs = require('fs'),
    ExpressPeerServer = require('peer').ExpressPeerServer; // Permet de récupérer les paramètres d'une requête POST


var storage = multer.diskStorage({
    destination: function (req, file, callback) {
        callback(null, './uploads');
    },
    filename: function (req, file, callback) {
        var user = parseUserInfo(req.body);

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
    }
});

var upload = multer({storage: storage}).any();

function parseUserInfo(body) {
    var user = {
        firstname: ent.encode(body.inputFirstname),
        name: ent.encode(body.inputName),
        email: ent.encode(body.inputEmail),
        pseudo: ent.encode(body.inputPseudo),
        pwd: ent.encode(body.inputPassword),
        city: body.inputCity,
        soldes: 0,
        type: body.inputType,
        isOnline: false,
        imgProfil: null,
        emailParent: (body.inputType === "Student") ? ent.encode(body.inputEmailParent) : null,
        diplomes: (body.inputType === "Coach") ? 'dipl_' + ent.encode(body.inputPseudo) + '_' + Date.now() + '.pdf' : null,
        tarif: (body.inputType === "Coach") ? ent.encode(body.inputTarif) : null,
        isValid: (body.inputType === "Coach") ? false : true,
        matieres: (body.inputType === "Coach") ? body.inputMatiere : null
    }
    return user;
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

    collection.find({type: "Coach", isValid: true, matieres: matiere}).sort({tarif: 1}).toArray(
        function (err, docs) {
            assert.equal(err, null);
            callback(docs);
        }
    );
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
    //console.log(util.inspect(info, {showHidden: true, depth: null, colors: true}));

    collection.aggregate([
        {$match: {id_coach: ObjectId(info.id_coach)}},
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

    collection.aggregate([
        {$match: {id_coach: ObjectId(info.id_coach)}},
        {$match: {date: {$gt: new Date(info.dateNow)}}},
        {$sort: {date: 1}}
    ]).toArray(
        function (err, docs) {
            assert.equal(err, null);
            callback(docs);
        }
    );
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
        id_coach: ObjectId(info.idUser)
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
                res.jsonp({status: 'Success', docs});
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
        if (req.body.inputType == "Student") {
            var user = parseUserInfo(req.body);
            MongoClient.connect(urlDB, function (err, db) {
                assert.equal(err, null);

                insertUser(db, user, function (docs) {
                    res.jsonp(docs);
                    db.close();
                });
            });
        }
        if (err) {
            return res.send(err);
        }
        res.send("Inscription is done");
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
    var info = {"id_coach": req.body.id_coach, "dateLimit": req.body.dateLimit, "dateNow": req.body.dateNow};

    if (info.id_coach != null) {
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
    //res.setHeader('Content-Type', 'text/plain');

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
                //console.log(util.inspect(dataList, {showHidden: true, depth: null, colors: true}));
                /*
                 var file = fs.createReadStream(__dirname+'/datas/'+dataList[0]['data']);
                 var writableStream = fs.createWriteStream('file2.txt');
                 var stat = fs.statSync(__dirname+'/datas/'+dataList[0]['data']);
                 res.setHeader('Content-Length', stat.size);
                 res.setHeader('Content-Type', 'application/pdf');
                 res.setHeader('Content-Disposition', 'attachment; filename=exemple.pdf');
                 file.pipe(writableStream);
                 */
                //res.sendFile(__dirname+'/datas/'+dataList[0]['data']);
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

app.get('/getPseudoById/:idStudent', function (req, res) {
    var info = req.params.idStudent;

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
        console.log(util.inspect(waitingUsers, {showHidden: true, depth: null, colors: true}));
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

            console.log("======== Somme calculé pour cette séance: " + somme);
            console.log('======== Temps : ' + durationTime + ' [s] - ' + durationTime / 60 + ' [min]');

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
