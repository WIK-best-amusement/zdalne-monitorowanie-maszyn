<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ListenForUpdate implements MessageComponentInterface {
    public $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        if (!isset($_SESSION['listeningUsers'])) {
            $_SESSION['listeningUsers'] = [];
        }
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        //echo "New connection to ListenForUpdate! ({$conn->resourceId})\n";
        $_SESSION['listeningUsers'][$conn->resourceId]['conn'] = $conn;
        $conn->send(json_encode(['listen' => true]));
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $listenRequest = json_decode($msg);
        $_SESSION['listeningUsers'][$from->resourceId]['deviceId'] = $listenRequest->deviceId;
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        unset($_SESSION['listeningUsers'][$conn->resourceId]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        unset($_SESSION['listeningUsers'][$conn->resourceId]);
        $conn->close();
    }
}