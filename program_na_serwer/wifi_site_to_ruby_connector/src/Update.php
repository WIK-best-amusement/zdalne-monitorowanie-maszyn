<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Update implements MessageComponentInterface
{
    public $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;

        if (!isset($_SESSION['listeningUsers'])) {
            $_SESSION['listeningUsers'] = [];
        }
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $conn->send(json_encode(['connected' => true]));
        $this->logToFile(['resourceId' => $conn->resourceId, 'ip' => $this->get_client_ip(), 'time' => time()]);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $updateRequest = json_decode($msg);
        $updateRequest->deviceId = isset($updateRequest->deviceId) ? (int) $updateRequest->deviceId : 0;
        $updateRequest->optionId = isset($updateRequest->optionId) ? (int) $updateRequest->optionId : 0;
        $updateRequest->value = substr($updateRequest->value, 0, 10);

        $updatedAt = '';
        if (isset($updateRequest->updated_at)) {
            $updatedAt = date_create($updateRequest->updated_at);
            // $updatedAt = date_create("2017-02-03T10:38:46.000+01:00");
            $updatedAt = date_format($updatedAt,"Y-m-d H:i:s");
        }
        $updateRequest->updated_at = $updatedAt;

        if ($this->checkData($updateRequest)) {
            $this->sendMessage($updateRequest->deviceId, $updateRequest->optionId, $updateRequest->value, $updateRequest->updated_at);
            $from->send(json_encode(['send' => true, 'data' => json_encode($updateRequest)]));
            $send = true;
        } else {
            $from->send(json_encode(['send' => false, 'reason' => 'wrong data']));
            $send = false;
        }

        $this->logToFile(['resourceId' => $from->resourceId, 'time' => time(), 'send' => $send, 'data' => $updateRequest]);
    }

    private function logToFile(Array $message)
    {
        $message = json_encode($message);
        file_put_contents('update.log', $message . "\n", FILE_APPEND);
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }

    private function sendMessage($deviceId, $optionId, $value, $updatedAt)
    {
        foreach ($_SESSION['listeningUsers'] as $key => $data) {
            if ($deviceId == $data['deviceId']) {
                $data['conn']->send(json_encode(array('deviceId' => $deviceId, 'optionId' => $optionId, 'value' => $value, 'updatedAt' => $updatedAt)));
            }
        }
    }

    public function get_client_ip()
    {
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    /**
     * @param $updateRequest
     * @return bool
     */
    public function checkData($updateRequest)
    {
        if (is_object($updateRequest)) {
            if (!empty($updateRequest->deviceId) && !empty($updateRequest->optionId)) {
                return true;
            }
        }

        return false;
    }
}