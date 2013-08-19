#!/usr/bin/perl

=head1 NAME

 Servers::sql - i-MSCP MySQL Server implementation

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

package Servers::sql::mysql;

use strict;
use warnings;

use iMSCP::Debug;
use iMSCP::HooksManager;
use iMSCP::Database;
use parent 'Common::SingletonClass';

=head1 DESCRIPTION

 i-MSCP MySQL server implementation.

=head1 PUBLIC METHODS

=over 4

=item(\%data)

 Add SQL user

=cut

sub addSqlUser
{
	my $self = shift;
	my $data = shift;

	my $rs = $self->{'hooksManager'}->trigger('beforeAddSqlUser', $data);

	my $db = $self->getDb();
	my $db->{'RaiseError'} = 1;

	eval {
		# We must not depend on the NO_AUTO_CREATE_USER mode
		$db->do('CREATE USER ?@? IDENTIFIED BY ?',  undef, $data->{'username'}, $data->{'hostname'}, $data->{'pasword'});

		if(defined $data->{'ON_DB_NAME'}) {
			my $dbName = $self->{'db'}->quoteIdentifier($data->{'ON_DB_NAME'});
			$db->do("GRANT ALL PRIVILEGES ON $dbName.* TO ?@?", undef, $data->{'usernmae'}, $data->{'hostname'})
		}
	};

	my $db->{'RaiseError'} = 0;

	if($@) {
		error($@);
		return 1;
	}

	$self->{'hooksManager'}->trigger('afterAddSqlUser', $data);
}

=item(\%data)

 Delete SQL user

=cut

sub deleteSqlUser
{
	my $self = shift;
	my $data = shift;

	my $rs = $self->{'hooksManager'}->trigger('beforeDelSqlUser', $data);

	my $db = $self->getDb();
    my $db->{'RaiseError'} = 1;

    eval { $db->do('DROP USER ?@?', undef, $data->{'username'}, $data->{'hostname'}); };

	my $db->{'RaiseError'} = 0;

	if($@) {
		error($@);
		return 1;
	}

	$self->{'hooksManager'}->trigger('afterDelSqlUser', $data);
}

=item(\%data)

 Add SQL database

=cut

sub addSqlDatabase
{
	my $self = shift;
	my $data = shift;

	my $rs = $self->{'hooksManager'}->trigger('beforeAddSqlDb', $data);

	my $db = $self->getDb();
    my $db->{'RaiseError'} = 1;

    eval {
    	my $dbName = $self->{'db'}->quoteIdentifier($data->{'database_name'});
    	$db->do("CREATE DATABASE IF NOT EXISTS $dbName");
    };

	my $db->{'RaiseError'} = 0;

	if($@) {
		error($@);
		return 1;
	}

	$self->{'hooksManager'}->trigger('afterAddSqlDb', $data);
}

=item(\%data)

 Delete SQL user

=cut

sub deleteSqlDatabase
{
	my $self = shift;
	my $data = shift;

	my $rs = $self->{'hooksManager'}->trigger('beforeDelSqlDb', $data);

	my $db = $self->getDb();
    my $db->{'RaiseError'} = 1;

    eval {
    	my $dbName = $self->{'db'}->quoteIdentifier($data->{'database_name'});
    	$db->do("DROP DATABASE IF EXISTS $dbName");
    };

	my $db->{'RaiseError'} = 0;

	if($@) {
		error($@);
		return 1;
	}

	$self->{'hooksManager'}->trigger('afterDelSqlDb', $data);

	0;
}

=item _getDb()

 Return database connection

 Return DBI

=cut

sub getDb()
{
	my $self = shift;

	if(! defined $self->{'db'}) {
		$self->{'db'} = iMSCP::Database->factory('db' => 'mysql')->getRawDb();
	}

	$self->{'db'};
}

=back

=head1 PRIVATE METHODS

=over 4

=item

=cut

sub _init
{
	my $self = shift;

	$self->{'hooksManager'} = iMSCP::HooksManager->getInstance();

	$self->{'hooksManager'}->trigger('beforeSqlInit', $self, 'sql') and fatal('sql - beforeSqlInit hook has failed');

	$self->{'hooksManager'}->trigger('afterSqlInit', $self, 'sql') and fatal('sql - afterSqlInit hook has failed');

	$self;
}


=back

=head1 AUTHOR

 Laurent Declercq <l.declercq@nuxwin.com>

=cut

1;
