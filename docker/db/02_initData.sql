use `jmp`;

insert into `group` (`name`)
values ('Admin');

insert into `user` (`username`, `lastname`, `firstname`, `email`, `password`, `password_change`)
values ('admin', 'User', 'Admin', '', '', false);

insert into `membership` (`group_id`, `user_id`)
values ((select `id` from `group` where `name` = 'Admin'), (select `id` from `user` where `username` = 'admin'));

insert into `registration_state` (`name`, `reason_required`)
values ('Abgemeldet', true),
       ('Angemeldet', false),
       ('Vielleicht', true);

insert into `event_type` (`title`, `color`)
values ('Test', '#d6f936');

insert into `event` (`title`, `from`, `to`, `place`, `description`, `event_type_id`, `default_registration_state_id`)
values ('Test event',
        '2019-01-15 12:12:12',
        '2019-01-15 13:13:13',
        'GibmIT, Pratteln',
        'This is a test event!',
        (select `id` from `event_type` where `title` = 'Test'),
        (select `id` from `registration_state` where `name` = 'Angemeldet'));

insert into `event_has_group` (`event_id`, `group_id`)
values ((select `id` from `event` where `title` = 'Test event'), (select `id` from `group` where `name` = 'admin'));