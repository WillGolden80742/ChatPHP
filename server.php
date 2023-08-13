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
    protected $messageLimits;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
        $this->nickNameMap = [];
        $this->messageLimits = [];
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "Nova conexão! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        $nickNameFrom = $data['nickNameFrom'];

        // Check message rate limit
        if (!$this->isMessageAllowed($nickNameFrom)) {
            return;
        }

        $this->nickNameMap[$nickNameFrom] = $from;
        if (isset($data['nickNameFrom']) && isset($data['nickNameTo']) && isset($data['message'])) {
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
    }

    protected function isMessageAllowed($nickName)
    {
        $currentTime = time();
        if (!isset($this->messageLimits[$nickName])) {
            $this->messageLimits[$nickName] = $currentTime;
            return true;
        }

        $lastMessageTime = $this->messageLimits[$nickName];
        if ($currentTime - $lastMessageTime >= 3) {
            $this->messageLimits[$nickName] = $currentTime;
            return true;
        }

        return false;
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Conexão {$conn->resourceId} foi fechada\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
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
