<?php

require_once(INCLUDE_DIR."class.plugin.php");
require_once(INCLUDE_DIR ."class.signal.php");
require_once("config.php");

class AutoAssignTicket extends Plugin {
    var $config_class = "TicketAutoassignPluginConfig";

    function bootstrap() {
        // Capture the ticket creation signal and do autoassignment function call.
        Signal::connect("ticket.created", array($this, "doAutoAssign"));
    }

    function doAutoAssign($ticket) {
        $config = $this->getConfig();

        // Get values based on configuration
        $max_open_tickets = $config->get("max_open_tickets");
        $based_on = $config->get("based_on");
        $online_hours = $config->get("minimum_online");

        if(empty($online_hours) || !intval($online_hours)) {
            $online_hours = 0;
        }
        if(empty($max_open_tickets) || !intval($max_open_tickets)) {
            $max_open_tickets = 0;
        }

        // auto-assign ticket to a random staff.
        if(!$ticket->staff_id) {
            $rand_query = "SELECT ost_staff.staff_id FROM ost_staff LEFT JOIN ost_ticket ON (ost_ticket.staff_id = ost_staff.staff_id AND status_id = 1)";

            // If there's a team id, join with the team table.
            if($ticket->team_id) {
                $rand_query .= " LEFT JOIN ost_team_member ON (ost_team_member.staff_id = ost_staff.staff_id) ";
            }

            // If assignment is based on role permission, join with role table
            if($based_on == "role") {
                $rand_query .= " LEFT JOIN ost_role ON (ost_role.id = ost_staff.role_id) ";
            }

            $rand_query .= " WHERE onvacation = 0 AND isactive = 1 AND lastlogin >= DATE_SUB(NOW(), INTERVAL " . $online_hours . " HOUR) ";

            // If assignment is based on user permission check staff permission, else role permission
            if($based_on == "user") {
                $rand_query .= " AND ost_staff.permissions like '%ticket.autoassign%'";
            } else if($based_on == "role") {
                $rand_query .= " AND ost_role.permissions like '%ticket.autoassign%'";
            }

            // If team id is present, team takes precedence over department.
            if($ticket->team_id) {
                $rand_query .= " AND ost_team_member.team_id = $ticket->team_id";
            } else if($ticket->dept_id) {
                $rand_query .= " AND ost_staff.dept_id = $ticket->dept_id ";
            }

            // Find one random ID.
            $rand_query .= " GROUP BY ost_staff.staff_id HAVING COUNT(ost_ticket.ticket_id) <= $max_open_tickets ORDER BY RAND() LIMIT 1";
            $res = db_query($rand_query);

            // Assign only if there is one id. Otherwise ignore
            if($res && db_num_rows($res) == 1) {
                $row = db_fetch_row($res);
                if(is_array($row) && $sid = array_pop($row)) {
                    $ticket->assignToStaff($sid, "SYSTEM (Automatic assignment)", $alert=true);
                }
            }
        }
    }
}
