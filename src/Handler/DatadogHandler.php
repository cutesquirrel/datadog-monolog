<?php declare(strict_types=1);

namespace DatadogMonolog\Handler;

use DatadogMonolog\Formatter\DatadogFormatter;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\Curl\Util;
use Monolog\Logger;

/**
 * @author Christian Brückner <chris@chrico.info>
 * @author Etienne Voilliot <cutesquirrel.dev@gmail.com>
 *
 * @link   https://docs.datadoghq.com/api/?lang=bash#logs
 */
final class DatadogHandler extends AbstractProcessingHandler {
  const HOST_EU = 'http-intake.logs.datadoghq.eu';
  const HOST_US = 'http-intake.logs.datadoghq.com';


  /**
   * @var string
   */
  private $endpoint;

  /**
   * @param string     $apiKey   API key created from your datadog account.
   * @param string     $hostname Host name supplied by Datadog.
   * @param string     $appname  Application name supplied by Datadog.
   * @param string     $service  Service name supplied by Datadog.
   * @param string     $ddSource Source name supplied by Datadog.
   * @param bool       $ssl      Whether or not SSL encryption should be used.
   * @param int|string $level    The minimum logging level to trigger this handler.
   * @param bool       $bubble   Whether or not messages that are handled should bubble up the stack.
   * @param string     $host     One of existing listener hosts, by default 'listener.logz.io'
   *
   * @throws \LogicException If curl extension is not available.
   */
  public function __construct(
    string $apiKey,
    string $hostname = '', string $appname = '', string $service = '', string $ddSource = 'REST-API',
    bool $ssl = true,
    int $level = Logger::DEBUG,
    bool $bubble = true,
    string $host = self::HOST_EU
  ) {

    $this->apiKey = $apiKey;
    $this->endpoint = $ssl ? 'https://' . $host : 'http://' . $host;
    $this->endpoint .= '/v1/input/' . $apiKey;

    $this->hostname = $hostname;
    $this->appname = $appname;
    $this->service = $service;
    $this->ddSource = $ddSource;

    parent::__construct($level, $bubble);
  }

  protected function write(array $record) {
    $this->send($record['formatted']);
  }

  // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
  protected function send($data) {
    $handle = curl_init();

    curl_setopt($handle, CURLOPT_URL, $this->endpoint);
    curl_setopt($handle, CURLOPT_POST, true);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
    curl_setopt($handle, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

    Util::execute($handle);
  }

  public function handleBatch(array $records) {
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
  protected function getDefaultFormatter(): FormatterInterface {
    $formatter = new DatadogFormatter();

    if (!empty($this->hostname)) {
      $formatter->setHostname($this->hostname);
    }
    if (!empty($this->appname)) {
      $formatter->setAppname($this->appname);
    }
    if (!empty($this->service)) {
      $formatter->setService($this->service);
    }
    if (!empty($this->ddSource)) {
      $formatter->setDdSource($this->ddSource);
    }

    return $formatter;
  }
}
