<?php declare(strict_types=1);

namespace DatadogMonolog\Formatter;

use Monolog\Formatter\JsonFormatter;

/**
 * Encodes message information into JSON in a format compatible with Datadog.
 *
 * @author Christian Brückner <chris@chrico.info>
 * @author Etienne Voilliot <cutesquirrel.dev@gmail.com>
 */
class DatadogFormatter extends JsonFormatter {

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
   * @param int  $batchMode
   * @param bool $appendNewline
   */
  public function __construct(int $batchMode = self::BATCH_MODE_NEWLINES, bool $appendNewline = true) {
    parent::__construct($batchMode, $appendNewline);
  }

  /**
   * Set hostname
   *
   * @param string $hostname
   */
  public function setHostname(string $hostname) {
    $this->hostname = $hostname;
  }

  /**
   * Set appname
   *
   * @param string $appname
   */
  public function setAppname(string $appname) {
    $this->appname = $appname;
  }

  /**
   * Set service
   *
   * @param string $appname
   */
  public function setService(string $service) {
    $this->service = $service;
  }

  /**
   * Set ddSource
   *
   * @param string $ddSource
   */
  public function setDdSource(string $ddSource) {
    $this->ddSource = $ddSource;
  }

  /**
   * Appends the 'hostname' and 'appname' parameter for indexing by Datadog.
   *
   * @param array $record
   *
   * @see  \Monolog\Formatter\JsonFormatter::format()
   *
   * @return string
   */
  public function format(array $record): string {

    if (!empty($this->hostname)) {
      $record['hostname'] = $this->hostname;
    }
    if (!empty($this->appname)) {
      $record['appname'] = $this->appname;
    }
    if (!empty($this->service)) {
      $record['service'] = $this->service;
    }
    if (!empty($this->ddsource)) {
      $record['ddsource'] = $this->ddsource;
    }

    return parent::format($record);
  }
}
