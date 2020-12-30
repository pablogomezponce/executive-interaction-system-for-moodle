create view logstore as select id, action, courseid, component, userid, objectid, eventname from mdl_logstore_standard_log;

create view user as select firstname, id, username from mdl_user;

create view assign as select duedate, id, course from mdl_assign;

create view assign_submission as select assignment, userid, timemodified, status, id from mdl_assign_submission;

create view resource as select name, id from mdl_resource;

create view course as select id, fullname from mdl_course;

create view url as select name, id, course from mdl_url;

create view role_assignments as select userid, roleid, contextid from mdl_role_assignments;

create view user_enrolments as select userid, enrolid from mdl_user_enrolments;

create view role as select id, shortname from mdl_role;

create view context as select id, instanceid from mdl_context;

create view enrol as select courseid, id from mdl_enrol;


