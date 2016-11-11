var mysql=require('mysql');
var connection = mysql.createConnection({
    host    :'localhost',
    port : 3306,
    user : 'root',
    password : 'kdcsev113$db',
    database:'newvpn'
});

exports.get_tunnels=function(token){
    var query = connection.query('select * from customers_data',function(err,rows){
        return rows;
    });
};

