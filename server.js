const fs = require('fs');

var env = process.env.NODE_ENV || 'dev';
var server;
if (env == 'dev') {
	console.log('Starting HTTP');
	server = require('http').Server();
} else {
	console.log('Starting HTTPS');
	server = require('https').createServer({
		key: fs.readFileSync('/etc/letsencrypt/live/gw2nodes.com/privkey.pem'),
		cert: fs.readFileSync('/etc/letsencrypt/live/gw2nodes.com/fullchain.pem')
	});
}

var io = require('socket.io')(server);

var Redis = require('ioredis');
var redis = new Redis();

redis.subscribe('live');
redis.subscribe('map');

redis.on('message', function (channel, message) {
	message = JSON.parse(message);

	if (channel != 'live') {
		delete message.data.ip;
	}
	
	io.emit(channel + ':' + message.event, message.data);
});

server.listen(3000);
