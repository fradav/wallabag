<?php

namespace Wallabag\CoreBundle\Helper;

use Psr\Log\LoggerInterface;
use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;

/**
 * This is a proxy to crypt and decrypt password used by SiteCredential entity.
 * BTW, It might be re-use for sth else.
 */
class CryptoProxy
{
    private $logger;
    private $encryptionKey;

    public function __construct($encryptionKeyPath, LoggerInterface $logger)
    {
        $this->logger = $logger;

        if (!file_exists($encryptionKeyPath)) {
            $key = Key::createNewRandomKey();

            file_put_contents($encryptionKeyPath, $key->saveToAsciiSafeString());
            chmod($encryptionKeyPath, 0600);
        }

        $this->encryptionKey = file_get_contents($encryptionKeyPath);
    }

    /**
     * Ensure the given value will be crypted.
     *
     * @param string $secretValue Secret valye to crypt
     *
     * @return string
     */
    public function crypt($secretValue)
    {
        $this->logger->debug('Crypto: crypting value: '.$this->mask($secretValue));

        return Crypto::encrypt($secretValue, $this->loadKey());
    }

    /**
     * Ensure the given crypted value will be decrypted.
     *
     * @param string $cryptedValue The value to be decrypted
     *
     * @return string
     */
    public function decrypt($cryptedValue)
    {
        $this->logger->debug('Crypto: decrypting value: '.$this->mask($cryptedValue));

        try {
            return Crypto::decrypt($cryptedValue, $this->loadKey());
        } catch (WrongKeyOrModifiedCiphertextException $e) {
            throw new \RuntimeException('Decrypt fail: '.$e->getMessage());
        }
    }

    /**
     * Load the private key.
     *
     * @return string
     */
    private function loadKey()
    {
        return Key::loadFromAsciiSafeString($this->encryptionKey);
    }

    /**
     * Keep first and last character and put some stars in between.
     *
     * @param string $value Value to mask
     *
     * @return string
     */
    private function mask($value)
    {
        return $value[0].'*****'.$value[strlen($value) - 1];
    }
}
