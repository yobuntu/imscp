#!/usr/bin/perl

# i-MSCP - internet Multi Server Control Panel
# Copyright (C) 2010 - 2011 by internet Multi Server Control Panel
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
# @category		i-MSCP
# @copyright	2010 - 2012 by i-MSCP | http://i-mscp.net
# @author		Daniel Andreca <sci2tech@gmail.com>
# @version		SVN: $Id$
# @link			http://i-mscp.net i-MSCP Home Site
# @license		http://www.gnu.org/licenses/gpl-2.0.html GPL v2

package Addons::piwik;

use strict;
use warnings;
use Data::Dumper;
use iMSCP::Debug;

use vars qw/@ISA/;

@ISA = ('Common::SingletonClass');
use Common::SingletonClass;

sub _init{

	my $self				= shift;

	$self->{cfgDir}	= "$main::imscpConfig{'CONF_DIR'}/piwik";
	$self->{bkpDir}	= "$self->{cfgDir}/backup";
	$self->{wrkDir}	= "$self->{cfgDir}/working";
	$self->{tplDir}	= "$self->{cfgDir}/parts";

	0;
}

sub factory{ return Addons::piwik->new(); }

sub install{

	use Addons::piwik::installer;

	my $self	= shift;
	my $rs		= 0;
	Addons::piwik::installer->new()->install();

	$rs;
}

sub addDmn{

        my $self        = shift;
        my $data        = shift;
        my $rs          = 0;

        local $Data::Dumper::Terse = 1;
        debug("Data: ". (Dumper $data));

        my $errmsg = {
                'DMN_NAME'      => 'You must supply domain name!',
        };

        foreach(keys %{$errmsg}){
                error("$errmsg->{$_}") unless $data->{$_};
                return 1 unless $data->{$_};
        }

        if($main::imscpConfig{STATS_SERVER} =~ m/piwik/i){
                $rs |= $self->addPiwikCron($data)
        }
        $rs;
}


sub addPiwikCron{

        use iMSCP::File;
        use iMSCP::Templator;
        use Servers::cron;

        my $self        = shift;
        my $data        = shift;
        my $rs          = 0;

	#FIXME, we are programming the cron as root so it can read the logs
	# is there a more appropiete user?
        my $cron = Servers::cron->factory();
        $rs = $cron->addTask({
                MINUTE  => int(rand(61)),       #random number between 0..60
                HOUR    => int(rand(6)),        #random number between 0..5
                DAY             => '*',
                MONTH   => '*',
                DWEEK   => '*',
                USER    => 'root',
		COMMAND =>	"python /var/www/imscp/gui/public/tools/stats/misc/log-analytics/import_logs.py ".
				"--url={BASE_SERVER_VHOST_PREFIX}{BASE_SERVER_VHOST}/tools/stats   --add-sites-new-hosts ".
				"--enable-http-errors --enable-http-redirects --enable-static --enable-bots ".
				"/var/log/apache2/{DMN_NAME}-combined.log --show-progress --log-hostname={DMN_NAME}",
                TASKID  => "PIWIK:$data->{DMN_NAME}"
        });

        $rs;
}

1;
