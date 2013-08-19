#!/usr/bin/perl

=head1 NAME

 Modules:SqlDatabase - i-MSCP module responsible to handle SQL databases

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
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
#
# @category    i-MSCP
# @copyright   2010-2013 by i-MSCP | http://i-mscp.net
# @author      Laurent Declercq <l.declercq@nuxwin.com>
# @link        http://i-mscp.net i-MSCP Home Site
# @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2

package Modules::SqlDatabase;

use strict;
use warnings;

use iMSCP::Debug;
use iMSCP::Database;
use parent 'Modules::Abstract';

=head1 DESCRIPTION

 i-MSCP modules responsible to handle SQL database.

=head1 PUBLIC METHODS

=over 4

=item process($sqlDatabaseId)

 Process the given SQL user

 Return int 0 on success, other on failure

=cut

sub process($$)
{
	my ($self, $sqlDatabaseId) = @_;

	my $rs = $self->loadData($sqlDatabaseId);
	return $rs if $rs;

	if($self->{'status'} ~~ ['toadd', 'tochange']) {
		$rs = $self->add();

		@sql = (
			"UPDATE `sql_database` SET `sqld_status` = ? WHERE `sqld_id` = ?",
			($rs ? scalar getMessageByType('error') : 'ok'), $sqlDatabaseId
		);
	} elsif($self->{'status'} eq 'todelete') {
		$rs = $self->delete();

		if($rs) {
			@sql = (
				"UPDATE `sql_database` SET `sqld_status` = ? WHERE `sqld_id` = ?",
				scalar getMessageByType('error'), $self->{'id'}
			);
		} else {
			@sql = ("DELETE FROM `sql_database` WHERE `sqld_id` = ?", $sqlDatabaseId);
		}
	}

	my $rdata = iMSCP::Database->factory()->doQuery('dummy', @sql);
	unless(ref $rdata eq 'HASH') {
		error($rdata);
		return 1;
	}

	$rs;
}

=item buildSQLData()

 Build data for i-MSCP SQL server implementation

 Return int 0;

=cut

sub buildSQLData
{
	my $self = shift;

	$self->{'sqld'} = { DATABASE_NAME => $self->{'data'}->{'sqld_name'} };

	0;
}

=back

=head1 PRIVATE METHODS

=over 4

=item _init()

 Initialize module

 Return Modules::Database

=cut

sub _init
{
	my $self = shift;

	$self->{'type'} = 'SqlDatabase';

	$self;
}

=item _loadData($sqlDatabaseId)

 Load data from database

 Return int 0 on success, other on failure

=cut

sub _loadData($$)
{
	my $self = shift;
	my $sqlDatabaseId = shift;

	my $rdata = iMSCP::Database->factory()->doQuery(
		'sqld_id', 'SELECT * FROM `sql_database` WHERE `sqld_id` = ?', $sqlDatabaseId
	);
	unless(ref $rdata eq 'HASH') {
		error($rdata);
		return 1;
	}

	unless(exists $rdata->{$sqlDatabaseId}) {
		error("No SQL user in table sql_database has ID = $sqlDatabaseId");
		return 1;
	}

	$self->{'data'} = $rdata;

	0;
}

=back

=head1 AUTHOR

 Laurent Declercq <l.declercq@nuxwin.com>

=cut

1;
