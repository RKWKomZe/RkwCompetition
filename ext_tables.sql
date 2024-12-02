CREATE TABLE tx_rkwcompetition_domain_model_competition (
    title varchar(255) NOT NULL DEFAULT '',
    register_start int(11) NOT NULL DEFAULT '0',
    register_end int(11) NOT NULL DEFAULT '0',
    jury_access_end varchar(255) NOT NULL DEFAULT '',
    file_removal_end varchar(255) NOT NULL DEFAULT '',
    jury_add_data varchar(255) NOT NULL DEFAULT '',
    link_jury_declaration_confident varchar(255) NOT NULL DEFAULT '',
    allow_team_participation smallint(1) unsigned NOT NULL DEFAULT '0',
    reminder_interval int(11) NOT NULL DEFAULT '0',
    link_cond_participation varchar(255) NOT NULL DEFAULT '',
    link_privacy varchar(255) NOT NULL DEFAULT '',
    admin_member text NOT NULL,
    jury_member text NOT NULL,
    group_for_jury int(11) unsigned DEFAULT '0',
    group_for_user int(11) unsigned DEFAULT '0',
    sectors varchar(255) DEFAULT '' NOT NULL,
    register text NOT NULL
);

CREATE TABLE tx_rkwcompetition_domain_model_sector (
   title varchar(255) NOT NULL DEFAULT '',
   description text
);

CREATE TABLE tx_rkwcompetition_domain_model_register (
	salutation varchar(255) NOT NULL DEFAULT '',
	title int(11) NOT NULL DEFAULT '0',
	first_name varchar(255) NOT NULL DEFAULT '',
	last_name varchar(255) NOT NULL DEFAULT '',
	institution varchar(255) NOT NULL DEFAULT '',
	address varchar(255) NOT NULL DEFAULT '',
	zip int(11) NOT NULL DEFAULT '0',
	city varchar(255) NOT NULL DEFAULT '',
    telephone varchar(255) DEFAULT '' NOT NULL,
    email varchar(255) DEFAULT '' NOT NULL,
	contribution_title varchar(255) NOT NULL DEFAULT '',
	type_of_work int(11) NOT NULL DEFAULT '0',
	remark text NOT NULL DEFAULT '',
	privacy int(11) NOT NULL DEFAULT '0',
	conditions_of_participation int(11) NOT NULL DEFAULT '0',
	is_group_work int(11) NOT NULL DEFAULT '0',
	group_work_insurance int(11) NOT NULL DEFAULT '0',
	group_work_add_persons text NOT NULL DEFAULT '',

    admin_approved int(11) NOT NULL DEFAULT '0',
    admin_approved_by int(11) NOT NULL DEFAULT '0',
    admin_approved_at int(11) NOT NULL DEFAULT '0',

    admin_refused int(11) NOT NULL DEFAULT '0',
    admin_refused_by int(11) NOT NULL DEFAULT '0',
    admin_refused_text text NOT NULL DEFAULT '',
    admin_refused_at int(11) NOT NULL DEFAULT '0',

	upload int(11) unsigned DEFAULT '0',

    sector int(11) NOT NULL DEFAULT '0',
	competition int(11) unsigned DEFAULT '0' NOT NULL,
    frontend_user int(11) unsigned DEFAULT '0' NOT NULL,
);

CREATE TABLE tx_rkwcompetition_domain_model_upload (
	abstract int(11) unsigned NOT NULL DEFAULT '0',
	full int(11) unsigned NOT NULL DEFAULT '0',
	remark text NOT NULL DEFAULT ''
);


