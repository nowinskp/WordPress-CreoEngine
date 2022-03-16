<?php

namespace Wpce\Utils;

/**
 * General usage debug class with output formatting methods
 */
class Debug {

  /**
   * var_dump wrapper, outputting content via echo
   *
   * @param mixed $mixed
   * @return void
   */
	static function vd($mixed) {
		echo '<pre>';
		var_dump($mixed);
		echo '</pre>';
		return null;
	}

  /**
   * var_dump wrapper, returning the output
   *
   * @param mixed $mixed
   * @return void
   */
	static function rvd($mixed) {
		ob_start();
		var_dump($mixed);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	/**
	 * Output debug code to the Javascript console
	 */
	static function debugToConsole($data) {
		if(is_array($data) || is_object($data)) {
			echo( "<script>console.log('PHP:', ".json_encode($data).");</script>" );
		} else {
			echo( "<script>console.log('PHP:', '".$data."');</script>" );
		}
	}

  /**
   * Outputs nicely formatted debug code, adding some layer of interactivity
   *
   * @param mixed $mixed
   * @param boolean $collapse - should the output be collapsed by default
   * @return void
   */
	static function formattedDebug($mixed, $collapse = false) {
		$recursive = function($data, $level=0) use (&$recursive, $collapse) {
			global $argv;

			$isTerminal = isset($argv);

			if (!$isTerminal && $level == 0 && !defined("DUMP_DEBUG_SCRIPT")) {
				define("DUMP_DEBUG_SCRIPT", true);

				echo '<script language="Javascript">function toggleDisplay(id) {';
				echo 'var state = document.getElementById("container"+id).style.display;';
				echo 'document.getElementById("container"+id).style.display = state == "inline" ? "none" : "inline";';
				echo 'document.getElementById("plus"+id).style.display = state == "inline" ? "inline" : "none";';
				echo '}</script>'."\n";
			}

			$type = !is_string($data) && is_callable($data) ? "Callable" : ucfirst(gettype($data));
			$type_data = null;
			$type_color = null;
			$type_length = null;

			switch ($type) {
				case "String":
				$type_color = "green";
				$type_length = strlen($data);
				$type_data = "\"" . htmlentities($data) . "\""; break;

				case "Double":
				case "Float":
				$type = "Float";
				$type_color = "#0099c5";
				$type_length = strlen($data);
				$type_data = htmlentities($data); break;

				case "Integer":
				$type_color = "red";
				$type_length = strlen($data);
				$type_data = htmlentities($data); break;

				case "Boolean":
				$type_color = "#92008d";
				$type_length = strlen($data);
				$type_data = $data ? "TRUE" : "FALSE"; break;

				case "Null":
				case "NULL":
				$type_length = 0; break;

				case "Array":
				$type_length = count($data);
			}

			if (in_array($type, ['Object', 'Array'])) {
				$notEmpty = false;

				foreach($data as $key => $value) {
					if (!$notEmpty) {
						$notEmpty = true;

						if ($isTerminal) {
							echo $type . ($type_length !== null ? "(" . $type_length . ")" : "")."\n";

						} else {
							$id = substr(md5(rand().":".$key.":".$level), 0, 8);

							echo "<a href=\"javascript:toggleDisplay('". $id ."');\" style=\"text-decoration:none\">";
							echo "<span style='color:#666666'>" . $type . ($type_length !== null ? "(" . $type_length . ")" : "") . "</span>";
							echo "</a>";
							echo "<span id=\"plus". $id ."\" style=\"display: " . ($collapse ? "inline" : "none") . ";\">&nbsp;&#10549;</span>";
							echo "<div id=\"container". $id ."\" style=\"display: " . ($collapse ? "" : "inline") . ";\">";
							echo "<br />";
						}

						for ($i=0; $i <= $level; $i++) {
							echo $isTerminal ? "|    " : "<span style='color:black'>|</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						}

						echo $isTerminal ? "\n" : "<br />";
					}

					for ($i=0; $i <= $level; $i++) {
						echo $isTerminal ? "|    " : "<span style='color:black'>|</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					}

					echo $isTerminal ? "[" . $key . "] => " : "<span style='color:black'>[" . $key . "]&nbsp;=>&nbsp;</span>";

					call_user_func($recursive, $value, $level+1);
				}

				if ($notEmpty) {
					for ($i=0; $i <= $level; $i++) {
						echo $isTerminal ? "|    " : "<span style='color:black'>|</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					}

					if (!$isTerminal) {
						echo "</div>";
					}

				} else {
					echo $isTerminal ?
					$type . ($type_length !== null ? "(" . $type_length . ")" : "") . "  " :
					"<span style='color:#666666'>" . $type . ($type_length !== null ? "(" . $type_length . ")" : "") . "</span>&nbsp;&nbsp;";
				}

			} else {
				echo $isTerminal ?
				$type . ($type_length !== null ? "(" . $type_length . ")" : "") . "  " :
				"<span style='color:#666666'>" . $type . ($type_length !== null ? "(" . $type_length . ")" : "") . "</span>&nbsp;&nbsp;";

				if ($type_data != null) {
					echo $isTerminal ? $type_data : "<span style='color:" . $type_color . "'>" . $type_data . "</span>";
				}
			}

			echo $isTerminal ? "\n" : "<br />";
		};

		call_user_func($recursive, $mixed);
	}
}
