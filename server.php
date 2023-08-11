<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require 'vendor/autoload.php';

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $nickNameMap;

    public function __construct() {
        $this->clients = new \SplObjectStorage();
        $this->nickNameMap = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Nova conexão! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        $nickNameFrom = $data['nickNameFrom'];
        $this->nickNameMap[$nickNameFrom] = $from->resourceId;
        
        if (isset($data['nickNameFrom']) && isset($data['nickNameTo']) && isset($data['message'])) {
            $nickNameTo = $data['nickNameTo'];
            $message = $data['message'];

            foreach ($this->clients as $client) {
                if ($client->resourceId === $this->nickNameMap[$nickNameTo]) {
                    $client->send(json_encode([
                        'from' => $nickNameFrom,
                        'message' => $message
                    ]));
                    break;
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Conexão {$conn->resourceId} foi fechada\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Ocorreu um erro: {$e->getMessage()}\n";
        $conn->close();
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

echo "Servidor WebSocket iniciado na porta 8080...\n";
$server->run();
?>