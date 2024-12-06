// SERVER CODE (server.js)
const WebSocket = require('ws');
const server = new WebSocket.Server({ port: 5000 });

let players = {}; // Store player data using unique IDs

server.on('connection', (ws) => {
  // Assign a unique ID to each connected player
  const playerId = generateUniqueId();
  players[playerId] = { x: 0, y: 0 }; // Initialize player position

  // Notify the client of their player ID
  ws.send(JSON.stringify({ type: 'assignId', playerId }));

  // Handle messages from clients
  ws.on('message', (message) => {
    try {
      const data = JSON.parse(message);
      if (data.type === 'updatePosition') {
        // Update player's position
        players[playerId] = { x: data.x, y: data.y };

        // Broadcast updated positions to all players
        broadcastPlayerPositions();
      }
    } catch (e) {
      console.error('Error parsing message:', e);
    }
  });

  // Remove player when they disconnect
  ws.on('close', () => {
    delete players[playerId];
    broadcastPlayerPositions();
  });

  // Broadcast updated positions to all players
  function broadcastPlayerPositions() {
    const playerData = JSON.stringify({ type: 'players', players });
    server.clients.forEach((client) => {
      if (client.readyState === WebSocket.OPEN) {
        client.send(playerData);
      }
    });
  }
});

// Generate a unique ID for each player
function generateUniqueId() {
  return 'player-' + Math.random().toString(36).substr(2, 9);
}

console.log('WebSocket server is running on ws://localhost:5000');

