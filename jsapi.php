<?php
	if(isset($_REQUEST['dnsmasqLog']))
		dnsmasqLog();

	if(isset($_REQUEST['dnsmasqHosts']))
		dnsmasqHosts();

	function dnsmasqLog()
	{
		$lines = explode("\n", trim(`sudo grep ": query" /var/log/dnsmasq.log`));
		foreach($lines as $row => $line)
		{
			$line = trim($line);
			list($datetime, $rest) = explode(" dnsmasq[", $line, 2);
			list($crud, $rest) = explode("] ", $rest, 2);
			$lines[$row] = $datetime.": ".$rest;
		}

		echo trim(implode("\n", $lines));
	}

	function dnsmasqHosts()
	{
		$hostnames = array();

		$lines = explode("\n", trim(`sudo grep ": query" /var/log/dnsmasq.log`));
		foreach($lines as $row => $line)
		{
			$line = trim($line);
			list($datetime, $rest) = explode(" dnsmasq[", $line, 2);
			list($crud, $rest) = explode("] ", $rest, 2);
			list($query, $crud, $reqIP) = explode(" ", $rest, 3);
			$hostnames[$query]++;
		}

		$hosts = array();
		foreach($hostnames as $query => $count)
			$hosts[] = array('hostname' => $query, 'count' => $count);

		$hosts = array_sort($hosts, 'count', SORT_DESC);
		$lines = "<table>";
		foreach($hosts as $host)
			$lines .= "<tr><td>".$host['hostname']."</td><td>".$host['count']."</td></tr>\n";

		$lines .= "</table>";
		echo trim($lines);
	}

	function array_sort($array, $on, $order=SORT_ASC)
	{
		$new_array = array();
		$sortable_array = array();

		if(count($array) > 0)
		{
			foreach($array as $k => $v)
			{
				if(is_array($v))
				{
                			foreach($v as $k2 => $v2)
					{
						if($k2 == $on)
							$sortable_array[$k] = $v2;
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}

			switch($order)
			{
				case SORT_ASC:
					asort($sortable_array);
					break;
				case SORT_DESC:
					arsort($sortable_array);
					break;
			}

			foreach($sortable_array as $k => $v)
				array_push($new_array, $array[$k]);
		}

		return $new_array;
	}
