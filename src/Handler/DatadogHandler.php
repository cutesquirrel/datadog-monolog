<?php declare( strict_types=1 );

namespace cutesquirrel\Handler;

use cutesquirrel\DatadogMonolog\Formatter\DatadogFormatter;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\Curl\Util;
use Monolog\Logger;

/**
 * @author Christian Brückner <chris@chrico.info>
 * @author Etienne VOILLIOT <cutesquirrel.dev@gmail.com>
 *
 * @link https://docs.datadoghq.com/api/?lang=bash#logs
 */
final class DatadogHandler extends AbstractProcessingHandler
{
    const HOST_EU = 'http-intake.logs.datadoghq.eu';
    const HOST_US = 'http-intake.logs.datadoghq.com';

    /**
     * @var string
     */
    private $token;
    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $endpoint;

    /**
     * @param string     $apiKey API key created from your datadog account.
     * @param bool       $ssl    Whether or not SSL encryption should be used.
     * @param int|string $level  The minimum logging level to trigger this handler.
     * @param bool       $bubble Whether or not messages that are handled should bubble up the stack.
     * @param string     $host   One of existing listener hosts, by default 'listener.logz.io'
     *
     * @throws \LogicException If curl extension is not available.
     */
    public function __construct(
        string $apiKey,
        bool $ssl = true,
        int $level = Logger::DEBUG,
        bool $bubble = true,
        string $host = self::HOST_EU
    ) {

        $this->apiKey = $apiKey;
        $this->endpoint = $ssl ? 'https://' . $host : 'http://' . $host ;
        $this->endpoint .= '/v1/input/'. $apiKey;

        parent::__construct($level, $bubble);
    }

    protected function write(array $record)
    {
        $this->send($record['formatted']);
    }

    // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
    protected function send($data)
    {
        $handle = curl_init();

        curl_setopt($handle, CURLOPT_URL, $this->endpoint);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        Util::execute($handle);
    }

    public function handleBatch(array $records)
    {
        $level = $this->level;
        $records = array_filter(
            $records,
            function (array $record) use ($level): bool {

                return ($record['level'] >= $level);
            }
        );

        if ($records) {
            $this->send(
                $this->getFormatter()
                    ->formatBatch($records)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    // phpcs:disable InpsydeCodingStandard.CodeQuality.NoAccessors.NoGetter
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new DatadogFormatter();
    }
}
