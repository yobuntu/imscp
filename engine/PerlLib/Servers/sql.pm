#!/usr/bin/perl

=head1 NAME

 Servers::sql - i-MSCP SQL Server implementation

=cut

# i-MSCP - internet Multi Server Control Panel
# Copyright (C) 2010-2013 by internet Multi Server Control Panel
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
#
# @category    i-MSCP
# @copyright   2010-2013 by i-MSCP | http://i-mscp.net
# @author      Laurent Declercq <l.declercq@nuxwin.com>
# @link        http://i-mscp.net i-MSCP Home Site
# @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2

package Servers::sql;

use strict;
use warnings;

=head1 DESCRIPTION

 i-MSCP SQL server implementation.

=head1 CLASS METHODS

=over 4

=item

 Factory

 Return Servers::sql

=cut

sub factory
{
	my $self = shift;
	my $server = shift || $main::imscpConfig{'SQL_SERVER'};
	my ($file, $class);

	if(lc($server) eq 'no') {
		$file = 'Servers/noserver.pm';
		$class = 'Servers::noserver';
	} else {
		$server =~ s/^([a-z]+).*/$1/i;
		$file = "Servers/sql/$server.pm";
		$class = "Servers::sql::$server";
	}

	require $file;
	$class->getInstance();
}

=back

=head1 AUTHOR

 Laurent Declercq <l.declercq@nuxwin.com>

=cut

1;
