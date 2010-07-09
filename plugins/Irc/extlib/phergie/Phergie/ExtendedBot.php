<?php
/**
 * StatusNet - the distributed open-source microblogging tool
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Extends the bot class (Phergie_Bot) to allow connection and access to
 * sockets and to allow StatusNet to 'drive' the bot
 *
 * @category  Phergie
 * @package   Phergie_Extended_Bot
 * @author    Luke Fitzgerald <lw.fitzgerald@googlemail.com>
 * @copyright 2010 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html AGPL 3.0
 * @link      http://status.net/
 */
class Phergie_Extended_Bot extends Phergie_Bot {
    /**
    * Set up bot and connect to servers
    *
    * @return void
    */
    public function connect() {
        $ui = $this->getUi();
        $ui->setEnabled($this->getConfig('ui.enabled'));

        $this->loadPlugins();
        $this->loadConnections();
    }

    /**
    * Get the sockets used by the bot
    *
    * @return array Array of socket resources
    */
    public function getSockets() {
        return $this->getDriver()->getSockets();
    }
}