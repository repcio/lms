<?php

/*
 * LMS version 1.11-cvs
 *
 *  (C) Copyright 2001-2012 LMS Developers
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

if(!($ticketid = $DB->GetOne('SELECT ticketid FROM rtnotes WHERE id = ?', array(intval($_GET['id'])))))
{
	$SESSION->redirect('?'.$SESSION->get('backto'));
}

$rights = $LMS->GetUserRightsRT($AUTH->id, 0, $ticketid);

if(($rights & 4) != 4)
{
	$SMARTY->display('noaccess.html');
	$SESSION->close();
	die;
}

$DB->Execute('DELETE FROM rtnotes WHERE id = ?', array(intval($_GET['id'])));

$SESSION->redirect('?m=rtticketview&id='.$ticketid);

?>
