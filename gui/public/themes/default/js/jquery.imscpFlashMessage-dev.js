/**
 * i-MSCP - internet Multi Server Control Panel
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @category    iMSCP
 * @package     iMSCP_Core
 * @subpackage  Layout
 * @copyright   2010-2013 by i-MSCP | http://i-mscp.net
 * @link        http://i-mscp.net
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 */

(function($, undefined) {
    $.fn.imscpFlashMessage = function (message, messageLevel, options) {
        this.queue('imscpFlashMessage', function (next) {
            var settings = $.extend(
                    {},
                    {
                        animationSpeed : 1000,
                        timeout : 5000
                    },
                    options
            );

            if(messageLevel == undefined) {
                messageLevel = 'info';
            }

            if (typeof(message) === 'string') {
                var $this = $(this);

                // Set message timeout
                setTimeout(function () {
                    $this.fadeOut(settings.animationSpeed, function () {
                        $this.removeClass(messageLevel);
                        next(); // Show next message from queu if any
                    });
                }, settings.timeout);

                $this.html(message).addClass(messageLevel).fadeIn(settings.animationSpeed);
            }
        });

        if (this.queue('imscpFlashMessage').length === 1 && this.is(":hidden")) {
            this.dequeue('imscpFlashMessage');
        }

        return this;
    };
})(jQuery);
