<?php declare(strict_types=1);

namespace DatadogMonolog\Formatter;

use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;

/**
 * Encodes message information into JSON in a format compatible with Datadog.
 *
 * @author Christian Brückner <chris@chrico.info>
 * @author Etienne Voilliot <cutesquirrel.dev@gmail.com>
 */
class DatadogFormatter extends JsonFormatter
{

  /**
   * @param string
   */
  protected $hostname = '';

  /**
   * @param string
   */
  protected $appname = '';

  /**
   * @param string
   */
  protected $service = '';
  /**
   * @param string
   */
  protected $ddSource = '';

  /**
   * Overrides the default batch mode to new lines for compatibility with the Datadog bulk API.
   *
   * @param int $batchMode
   * @param bool $appendNewline
   */
  public function __construct(int $batchMode = self::BATCH_MODE_NEWLINES, bool $appendNewline = true)
  {
    parent::__construct($batchMode, $appendNewline);
  }

  /**
   * Set hostname
   *
   * @param string $hostname
   */
  public function setHostname(string $hostname)
  {
    $this->hostname = $hostname;
  }

  /**
   * Set appname
   *
   * @param string $appname
   */
  public function setAppname(string $appname)
  {
    $this->appname = $appname;
  }

  /**
   * Set service
   *
   * @param string $appname
   */
  public function setService(string $service)
  {
    $this->service = $service;
  }

  /**
   * Set ddSource
   *
   * @param string $ddSource
   */
  public function setDdSource(string $ddSource)
  {
    $this->ddSource = $ddSource;
  }


  /**
   * Appends the 'timestamp' parameter for indexing by Loggly.
   *
   * @see https://www.loggly.com/docs/automated-parsing/#json
   * @see \Monolog\Formatter\JsonFormatter::format()
   */
  protected function normalizeRecord(LogRecord $record): array
  {
    $recordData = parent::normalizeRecord($record);

    if (!empty($this->hostname)) {
      $recordData['hostname'] = $this->hostname;
    }
    if (!empty($this->appname)) {
      $recordData['appname'] = $this->appname;
    }
    if (!empty($this->service)) {
      $recordData['service'] = $this->service;
    }
    if (!empty($this->ddSource)) {
      $recordData['ddSource'] = $this->ddSource;
    }

    return $recordData;
  }
}
