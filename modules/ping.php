<?php

/*
 * LMS version 1.11-cvs
 *
 *  (C) Copyright 2001-2011 LMS Developers
 *
 *  Please, see the doc/AUTHORS for more information about authors!
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License Version 2 as
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 *  USA.
 *
 *  $Id$
 */

function refresh($params)
{
	// xajax response
	$objResponse = new xajaxResponse();

	$ipaddr = $params['ipaddr'];
	$received = $params['received'];
	$transmitted = $params['transmitted'];
	if (empty($CONFIG['phpui']['ping_helper']))
		$cmd = 'sudo ping %i -c 1 -s 1450 -w 1.0';
	else
		$cmd = $CONFIG['phpui']['ping_helper'];
	$cmd = preg_replace('/%i/', $ipaddr, $cmd);
	exec($cmd, $output);
	$transmitted++;
	$reply = preg_grep('/icmp_[rs]eq/', $output);
	if (count($reply))
	{
		$received++;
		if (preg_match('/^([0-9]+).+ttl=([0-9]+).+time=([0-9\.]+.+)$/', current($reply), $matches))
			$output = trans('$a bytes from $b: icmp_req=$c ttl=$d time=$e',
				$matches[1], $ipaddr, $transmitted, $matches[2], $matches[3]);
	}
	else
		$output = trans('Destination Host Unreachable');
	if (empty($received))
		$received = '0';
	$objResponse->append('data', 'innerHTML', $output.'<br>');
	$objResponse->assign('transmitted', 'value', $transmitted);
	$objResponse->assign('received', 'value', $received);
	$objResponse->assign('summary', 'innerHTML', '<b>'.trans('Total: $a% ($b/$c)', 
		round(($received / $transmitted) * 100), $received, $transmitted).'</b>');
	$objResponse->call('ping_reply');
	return $objResponse;
}

$layout['pagetitle'] = trans('Ping');

if (isset($_GET['p']))
{
	$SMARTY->assign('part', $_GET['p']);
	switch ($_GET['p']) {
		case 'main':
			/* Using AJAX for template plugins */
			require(LIB_DIR.'/xajax/xajax_core/xajax.inc.php');

			$xajax = new xajax();
			$xajax->configure('errorHandler', true);
			$xajax->configure('javascript URI', 'img');
			$xajax->register(XAJAX_FUNCTION, 'refresh');
			$xajax->processRequest();

			$SMARTY->assign('xajax', $xajax->getJavascript());
			break;
		case 'titlebar':
		case 'ipform':
			if (!isset($_GET['popupid']))
				die;
			$SMARTY->assign('popupid', $_GET['popupid']);
			break;
	}
}

if (isset($_GET['ip']) && check_ip($_GET['ip']))
	$SMARTY->assign('ipaddr', $_GET['ip']);

$SMARTY->display('ping.html');

?>