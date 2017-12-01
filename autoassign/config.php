<?php

require_once INCLUDE_DIR . "class.plugin.php";

class TicketAutoassignPluginConfig extends PluginConfig {
    function getOptions() {
        // Options to show in configuration
        return array(
            "title" => new SectionBreakField(array(
                "label" => "Automatic random ticket assignment plugin (department, team)",
            )),
            "based_on" => new ChoiceField([
                "label" => "Enable autoassignment based on",
                "hint" => "Role permission / User permission (you can set permission in both roles and in users).",
                "choices" => array(
                    "role" => "Role permission",
                    "user" => "User permission"
                ),
                "default" => "role"
            ]),
            "minimum_online" => new TextboxField(array(
                "placeholder" => "Hours",
                "validator" => "number",
                "label" => "Maximum agent inactivity period (hours)",
                "hint" => "Only assign tickets to agents who have logged in at least once in the last N hours",
                "configuration" => array("size" => 3, "length" => 3, "html" => FALSE),
                "default" => 12
            )),
            "max_open_tickets" => new TextboxField(array(
                "validator" => "number",
                "label" => "Maximum open tickets per agent",
                "hint" => "Only assign tickets to agents who have less than these many open tickets",
                "configuration" => array("size" => 5, "length" => 5, "html" => FALSE),
                "default" => 150
            ))
    );
    }
}
