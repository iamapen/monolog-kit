<?php

namespace Iamapen\MonologKit\Monolog\Handler;

use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\NormalizerFormatter;

/**
 * CSV Stream Handler
 *
 * - 日時、メッセージ、context、extra をフラットなCSVで出力する。
 * - context, extra は常に同じ構造である必要がある
 * - 固定のフォーマットを表形式で出力したい場合に使えるかもしれない
 */
class CsvStreamHandler extends StreamHandler {
	protected $delimiter = ',';
	protected $enclosure = '"';
	protected $escapeChar = '\\';

	protected $headerOutputted = false;


	protected function streamWrite($stream, array $record) {
		$csvRow = [
			'datetime' => $record['datetime']->format(NormalizerFormatter::SIMPLE_DATE),
			'localtime' => $record['datetime']->format('Y-m-d H:i:s.u'),
			'message' => $record['message'],
		];

		$formatter = $this->getDefaultFormatter();
		// context
		foreach ($record['context'] as $name => $val) {
			$csvRow['cx_' . $name] = $formatter->stringify($val);

		}
		// extra
		foreach ($record['extra'] as $name => $val) {
			$csvRow['ex_' . $name] = $formatter->stringify($val);
		}

		// header row
		if (!$this->headerOutputted) {
			$csvHeader = array_combine(array_keys($csvRow), array_keys($csvRow));
			if (version_compare(PHP_VERSION, '5.5.4', '>=')) {
				fputcsv($stream, $csvHeader, $this->delimiter, $this->enclosure, $this->escapeChar);
			} else {
				fputcsv($stream, $csvHeader, $this->delimiter, $this->enclosure);
			}
			$this->headerOutputted = true;
		}

		if (version_compare(PHP_VERSION, '5.5.4', '>=')) {
			fputcsv($stream, $csvRow, $this->delimiter, $this->enclosure, $this->escapeChar);
		} else {
			fputcsv($stream, $csvRow, $this->delimiter, $this->enclosure);
		}
	}

	/**
	 * @param string $delimiter
	 * @return static
	 */
	public function setDelimiter($delimiter) {
		$this->delimiter = $delimiter;
		return $this;
	}

	/**
	 * @param string $enclosure
	 * @return static
	 */
	public function setEnclosure($enclosure) {
		$this->enclosure = $enclosure;
		return $this;
	}

	/**
	 * @param string $escapeChar
	 * @return static
	 */
	public function setEscapeChar($escapeChar) {
		$this->escapeChar = $escapeChar;
		return $this;
	}

	/**
	 * @return LineFormatter
	 */
	protected function getDefaultFormatter() {
		return new LineFormatter();
	}
}
