<?php
/**
 * This file is part of ProjectOrganizer.
 *
 * ProjectOrganizer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ProjectOrganizer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ProjectOrganizer.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category TYPO3 Extension
 * @package  Project Organizer
 * @author   Alexander Gunkel <alexandergunkel@gmail.com>
 * @license  GPL
 * @link     http://www.gnu.org/licenses/
 */
return array(
    'ctrl' => [
        'title' => 'FE User',
        'label' => 'last_name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'hideTable' => true,
        'security' => [
            'ignoreWebMountRestriction' => false,
            'ignoreRootLevelRestriction' => false,
        ],
        'searchFields' => 'last_name'
    ],
    'interfaces' => array(
        'showRecordFieldList' => 'first_name, last_name, email'
    ),
    'columns' => array(
        'first_name' => [
            'label' => 'First name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim, required',
            ],
        ],
        'last_name' => [
            'label' => 'last name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim, required',
            ],
        ],
        'email' => [
            'label' => 'E-Mail',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'email, required',
            ],
        ],
        'company' => [
            'label' => 'Unternehmen/Einrichtung',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim, required',
            ],
        ],
        'position' => [
            'label' => 'Position / Bereich',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim, required',
            ],
        ],
        'password' => [
            'label' => 'Details & Erfahrungen',
            'config' => [
                'type' => 'text',
                'size' => 20,
                'eval' => 'trim, required',
            ],
        ],
        'registration_state' => [
            'label' => 'Status',
            'config' => [
                'type' => 'text',
                'size' => 10,
                'eval' => 'trim, required',
            ],
        ],
    ),
    'types' => [
        '1' => ['showitem' => 'first_name, last_name, email, company, position'],
    ],
    'palettes' => array(
    ),
);

?>
