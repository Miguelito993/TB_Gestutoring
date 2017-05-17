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
        cors = require('cors');


app.use(morgan('combined')); // Log dans la console

app.use(cors()); // Autorise toutes les CORS Requests

app.get('/sous-sol', function (req, res) {
    res.setHeader('Content-Type', 'text/plain');

    MongoClient.connect(urlDB, function (err, db) {
        console.log("Connected successfully to server");
        db.close();
    });

    res.json({vin: 'marques'});
});

app.use(function (req, res, next) {
    res.setHeader('Content-Type', 'text/plain');
    res.status(404).send('Page introuvable !\n');
});

server.listen(PORT, HOSTNAME, () => {
    console.log('Serveur: http://' + HOSTNAME + ':' + PORT);
});
