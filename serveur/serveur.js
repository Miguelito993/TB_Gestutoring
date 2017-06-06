const HOSTNAME = '127.0.0.1';
const PORT = 4242;

const HOST_DB = 'localhost'
const PORT_DB = 27017;
const NAME_DB = 'db_gestutoring';

var urlDB = 'mongodb://' + HOST_DB + ':' + PORT_DB + '/' + NAME_DB;

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
        ExpressPeerServer = require('peer').ExpressPeerServer; // Permet de récupérer les paramètres d'une requête POST
        

var storage = multer.diskStorage({
    destination: function(req, file, callback){
        callback(null, './uploads');
    },
    filename: function(req, file, callback){       
        var user = parseUserInfo(req.body);
               
        MongoClient.connect(urlDB, function (err, db) {
            assert.equal(err, null);
            
            // Impose un timer dans le cas où l'utilisateur charge plusieurs diplômes à faire valider
            setTimeout(checkLogin(db, {pseudo: user.pseudo, password: user.pwd}, function(docs){
                if(docs[0] == null){                   
                   // Ajouter le nouvel utilisateur avec le nouveau diplome
                   insertUser(db, user); 
                }else{
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

function parseUserInfo(body){    
    var user = {        
            firstname : ent.encode(body.inputFirstname),
            name : ent.encode(body.inputName),
            email : ent.encode(body.inputEmail),
            pseudo : ent.encode(body.inputPseudo),
            pwd : ent.encode(body.inputPassword),
            city : body.inputCity,
            soldes : 0,
            type : body.inputType,
            isOnline : false,
            imgProfil : null,
            emailParent : (body.inputType === "Student")?ent.encode(body.inputEmailParent):null,
            diplomes : (body.inputType === "Coach")?'dipl_' + ent.encode(body.inputPseudo) + '_' + Date.now() + '.pdf':null,        
            tarif : (body.inputType === "Coach")?ent.encode(body.inputTarif):null,
            isValid : (body.inputType === "Coach")?false:true,
            matieres : (body.inputType === "Coach")?body.inputMatiere:null
        }    
    return user;
}

var getDepartments = function (db, callback) {
    var collection = db.collection('t_departments');

    collection.find({}).toArray(function (err, docs) {
        assert.equal(err, null);
        callback(docs);
    });
}

var getMatieres = function (db, callback) {
    var collection = db.collection('t_matieres');

    collection.find({}, {name: 1}).toArray(function (err, docs) {
        assert.equal(err, null);
        callback(docs);
    });
}

var checkLogin = function (db, user, callback) {
    var collection = db.collection('t_users');
    
    collection.find({pseudo: user.pseudo, password: user.password}).toArray(function (err, docs) {
        assert.equal(err, null);       
        callback(docs);
    });
}

var changeStatus = function (db, info, callback) {
    var collection = db.collection('t_users');
        
    collection.update({ "_id": ObjectId(info.id) }, { $set: {"isOnline": info.statusOnline} }, 
        function(err, docs) {
            assert.equal(err, null);
            callback(docs);
        }
    );
}

var getCoaches = function (db, matiere, callback) {
    var collection = db.collection('t_users');

    collection.find({type: "Coach", isValid : true, matieres: matiere}).toArray(function (err, docs) {
        assert.equal(err, null);
        callback(docs);
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
        diplomes: [ user.diplomes ],
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
        
    collection.update({ "_id": ObjectId(user.id) }, { $push: {"diplomes" : user.diplomes} }, 
        function(err) {
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

        getMatieres(db, function (docs) {
            res.jsonp(docs);
            db.close();
        });
    });
});

app.post('/checkLogin', function (req, res) {
    var user = {"pseudo": req.body.pseudo, "password": req.body.pwd };
            
    MongoClient.connect(urlDB, function (err, db) {
       assert.equal(err, null);

       checkLogin(db, user, function(docs){             
        if(docs[0] != null){  
            res.jsonp({status: 'Success', docs});
        }else{ 
            res.jsonp({status: 'Failed'});
        }           
        
        db.close();
       });        
    });
});

app.post('/changeStatus', function (req, res) {    
    var info = {"id": req.body.id, "statusOnline": req.body.statusOnline };
        
    if((info.id != null) && (info.statusOnline != null)){
        MongoClient.connect(urlDB, function (err, db) {
           assert.equal(err, null);

            changeStatus(db, info, function(docs){                
                res.jsonp(docs);
                db.close();
           });        
        });
    }
});

app.post('/submitInscription',function(req, res){    
    upload(req, res, function(err){
        if(req.body.inputType == "Student"){
            var user = parseUserInfo(req.body);
            MongoClient.connect(urlDB, function (err, db) {
                assert.equal(err, null);

                 insertUser(db, user, function(docs){                
                     res.jsonp(docs);
                     db.close();
                });        
             });
        }
        if(err){
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

app.use('/peerjs', ExpressPeerServer(server, {debug: true}));

app.use(function (req, res, next) {
    res.setHeader('Content-Type', 'text/plain');
    res.status(404).send('Page introuvable !\n');
});

server.listen(PORT, HOSTNAME, () => {
    console.log('Serveur: http://' + HOSTNAME + ':' + PORT);
});
