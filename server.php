<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require 'vendor/autoload.php';

class Chat implements MessageComponentInterface
{
    protected $clients;
    protected $nickNameMap;
    protected $messageRateLimit = 3; // Allow 3 messages per second

    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
        $this->nickNameMap = [];
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        if (!$this->isValidMessage($data)) {
            return;
        }
        
        $nickNameFrom = $data['nickNameFrom'];
        $this->nickNameMap[$nickNameFrom] = $from;

        if ($this->isRateLimited($from)) {
            return;
        }

        $nickNameTo = $data['nickNameTo'];
        $message = $data['message'];

        if (isset($this->nickNameMap[$nickNameTo])) {
            $client = $this->nickNameMap[$nickNameTo];
            $client->send(json_encode([
                'from' => $nickNameFrom,
                'message' => $message
            ]));
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has been closed\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    protected function isValidMessage($data)
    {
        // Implement your own validation logic here
        return isset($data['nickNameFrom']) && isset($data['nickNameTo']) && isset($data['message']);
    }

    protected function isRateLimited($conn)
    {
        // Implement rate limiting logic here
        // Track and compare the message rate for each connection
        return false; // Modify this based on your implementation
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8080
);

echo "WebSocket server started on port 8080...\n";
$server->run();
