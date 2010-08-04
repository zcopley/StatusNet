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
 * Talks to the Statusnet IM architecture to enqueue incoming message messages
 * and notify result of nickname registration checks
 *
 * @category  Phergie
 * @package   Phergie_Plugin_Statusnet
 * @author    Luke Fitzgerald <lw.fitzgerald@googlemail.com>
 * @copyright 2010 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html AGPL 3.0
 * @link      http://status.net/
 */

class Phergie_Plugin_Statusnet extends Phergie_Plugin_Abstract {
    /**
    * Message callback details
    *
    * @var array
    */
    protected $messageCallback;

    /**
    * Registration check callback details
    *
    * @var array
    */
    protected $regCallback;

    /**
    * Load callback from config
    */
    public function onLoad() {
        $messageCallback = $this->config['statusnet.messagecallback'];
        if (is_callable($messageCallback)) {
            $this->messageCallback = $messageCallback;
        } else {
            $this->messageCallback = NULL;
        }

        $regCallback = $this->config['statusnet.regcallback'];
        if (is_callable($regCallback)) {
            $this->regCallback = $regCallback;
        } else {
            $this->regCallback = NULL;
        }

        $this->unregRegexp = $this->getConfig('statusnet.unregregexp', '/\x02(.*?)\x02 (?:isn\'t|is not) registered/i');
        $this->regRegexp = $this->getConfig('statusnet.regregexp', '/(?:\A|\x02)(\w+?)\x02? (?:\(account|is \w+?\z)/i');
    }

    /**
     * Passes incoming messages to StatusNet
     *
     * @return void
     */
    public function onPrivmsg() {
        if ($this->messageCallback !== NULL) {
            $event = $this->getEvent();
            $source = $event->getSource();
            $message = trim($event->getText());

            call_user_func($this->messageCallback, array('sender' => $source, 'message' => $message));
        }
    }

    /**
     * Catches the response from NickServ
     *
     * @return void
     */
    public function onNotice() {
        $event = $this->getEvent();
        if ($event->getNick() == 'NickServ') {
            $message = $event->getArgument(1);
            if (preg_match($this->unregRegexp, $message, $groups)) {
                $screenname = $groups[1];
                call_user_func($this->regCallback, array('screenname' => $screenname, 'registered' => false));
            } elseif (preg_match($this->regRegexp, $message, $groups)) {
                $screenname = $groups[1];
                call_user_func($this->regCallback, array('screenname' => $screenname, 'registered' => true));
            }
        }
    }
}
