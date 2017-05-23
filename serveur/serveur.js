const HOSTNAME = '127.0.0.1';
const PORT = 4242;

const PORT_DB = 27017;
const NAME_DB = 'db_gestutoring';

var urlDB = 'mongodb://localhost:' + PORT_DB + '/' + NAME_DB;

var app = require('express')(),
        server = require('http').createServer(app),
        io = require('socket.io').listen(server),
        ent = require('ent'), // Permet de bloquer les caractères HTML (sécurité équivalente à htmlentities en PHP)
        morgan = require('morgan'), // Utile pour les logs
        MongoClient = require('mongodb').MongoClient, // Accès à la base de données
        ObjectId = require('mongodb').ObjectId,
        cors = require('cors'), // Autorise les requêtes cross-domain
        assert = require('assert'),
        bodyParser = require('body-parser'), // Permet de récupérer les paramètres d'une requête POST
        fs = require('fs');


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
    /*
    var firstname = req.body.firstname,
        name = req.body.name,
        email = req.body.email,
        pseudo = req.body.pseudo,
        pwd = req.body.pwd,
        city = req.body.city,
        soldes = 0,
        type = req.body.type,
        isOnline = false;
    //var imgProfil = req.body.imgProfil;
    if(type == "Student"){
        var emailParent = req.body.emailParent;
    }else if(type == "Coach"){
        var diplomes = req.body.diplomes,
            tarif = req.body.tarif,
            isValid = false,
            matieres = req.body.matieres;
    }
     */
   /*
    * https://codeforgeek.com/2014/11/file-uploads-using-node-js/
    * Suivre cet exemple pour recupèrer les fichiers
    * 
    * Exemple disponible dans le dossier Téléchargements
    */
    
    
    
});

app.use(function (req, res, next) {
    res.setHeader('Content-Type', 'text/plain');
    res.status(404).send('Page introuvable !\n');
});

server.listen(PORT, HOSTNAME, () => {
    console.log('Serveur: http://' + HOSTNAME + ':' + PORT);
});
