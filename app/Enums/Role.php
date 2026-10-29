<?php

namespace App\Enums;

/**
 * Rolle einer Person, global (users.role_id = Rolle im aktuellen Kurs) und pro Kurs (camp_users.role_id).
 * Die Werte sind die IDs der Tabelle roles; dort stehen Anzeigename und Rechte (is_admin, is_campleader,
 * is_leader), die User::isAdmin(), isCampleader() und isLeader() auswerten.
 */
enum Role: int
{
    case Administrator = 1;
    case Kursleiter = 2;
    case Gruppenleiter = 3;
    case Teilnehmer = 4;
    case Stabsleiter = 5;
}
