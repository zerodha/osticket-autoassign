<?php

$desc = "Ability to receive automatic ticket assignments from the system?";
RolePermission::register("Miscellaneous", ["ticket.autoassign" => ["title" => "Automatic assignment",
                                    "desc" => $desc, "primary" => true]], true);
RolePermission::register("Tickets", ["ticket.autoassign" => ["title" => "Automatic assignment",
                                    "desc" => $desc, "primary" => false]], true);

return array(
    "id" =>             "ticket:autoassign",
    "version" =>        "0.1",
    "name" =>           "Automatic ticket assignment",
    "author" =>         "Vishnu S",
    "description" =>    "Assign tickets automatically to random agents based on teams, department, and activity.",
    "url" =>            "https://vishnus.in",
    "plugin" =>         "autoassign.php:AutoAssignTicket"
);

