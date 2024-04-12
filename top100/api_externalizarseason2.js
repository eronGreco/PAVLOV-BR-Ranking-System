const express = require('express');
const mysql = require('mysql');
const dbConfig = require('/home/erongrecomelo/dbconfigPAVLOV.php'); // Ajuste o caminho conforme necessário

// Conectar ao banco de dados
const db = mysql.createConnection(dbConfig);

db.connect((err) => {
    if (err) {
        console.error('Erro ao conectar ao banco de dados: ', err);
        process.exit(1);
    }
    console.log('Conectado ao banco de dados MySQL');
});

const app = express();

// Middleware
app.use(express.json());

// Rota de teste
app.get('/', (req, res) => {
    res.send('API está funcionando!');
});

// Iniciar o servidor
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`Servidor rodando na porta ${PORT}`);
});
