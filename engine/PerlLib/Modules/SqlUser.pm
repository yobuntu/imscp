#!/usr/bin/perl

=head1 NAME

 Modules:SqlUser - i-MSCP module responsible to handle SQL users

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

package Modules::SqlUser;

use strict;
use warnings;

use iMSCP::Debug;
use iMSCP::Database;
use parent 'Modules::Abstract';

=head1 DESCRIPTION

 i-MSCP modules responsible to handle i-MSCP customer SQL users.

=head1 PUBLIC METHODS

=over 4

=item process($sqlUserId)

 Process the given SQL user

 Return int 0 on success, other on failure

=cut

sub process($$)
{
	my ($self, $sqlUserId) = @_;

	my $rs = $self->loadData($sqlUserId);
	return $rs if $rs;

	if($self->{'status'} ~~ ['toadd', 'tochange']) {
		$rs = $self->add();

		@sql = (
			"UPDATE `sql_user` SET `sqlu_status` = ? WHERE `sqlu_id` = ?",
			($rs ? scalar getMessageByType('error') : 'ok'), $sqlUserId
		);
	} elsif($self->{'status'} eq 'todelete') {
		$rs = $self->delete();

		if($rs) {
			@sql = (
				"UPDATE `sql_user` SET `sqlu_status` = ? WHERE `sqlu_id` = ?",
				scalar getMessageByType('error'), $self->{'id'}
			);
		} else {
			@sql = ("DELETE FROM `sql_user` WHERE `sqlu_id` = ?", $sqlUserId);
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

 Build data from i-MSCP SQL server implementation

 Return int 0;

=cut

sub buildSQLData
{
	my $self = shift;

	$self->{'sqld'} = {
		USER => $self->{'data'}->{'sqlu_name'},
		PASSWORD => $self->{'data'}->{'sqlu_pass'},
		ON_DB_NAME => $self-> $self->{'data'}->{'sqld_name'}
	};

	0;
}

=back

=head1 PRIVATE METHODS

=over 4

=item _init()

 Initialize module

 Return Modules::SqlUser

=cut

sub _init
{
	my $self = shift;

	$self->{'type'} = 'SqlUser';

	$self;
}

=item _loadData($sqlUserId)

 Load data from database

 Return int 0 on success, other on failure

=cut

sub _loadData($$)
{
	my $self = shift;
	my $sqlUserId = shift;

	my $rdata = iMSCP::Database->factory()->doQuery(
		'sqlu_id',
		'
			SELECT
				`t1`.*, `t2`.*
			FROM
				`sql_user` AS `t1`
			INNER JOIN
				`sql_database` AS `t2` ON(`t2`.`sqld_id` = `t1`.`sqld_id`)
			WHERE
				`sqlu_id`= ?
			AND
				`sqld_status` = 'ok'
		',
		$sqlUserId
	);

	unless(ref $rdata eq 'HASH') {
		error($rdata);
		return 1;
	}

	unless(exists $rdata->{$sqlUserId}) {
		error("No SQL user in table sql_user has ID = $sqlUserId or parent database is missing");
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
