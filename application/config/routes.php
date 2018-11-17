<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
(:any)
(:num)
*/
$route['default_controller'] 	= 'v2/home';
$route['404_override'] 			= '';
$route['translate_uri_dashes'] 	= FALSE;

# V2
$route['license'] 				= 'license';
$route['auth'] 					= 'auth';
$route['logout'] 				= 'auth/logout';
$route['permission'] 			= 'v2/home/permission';
$route['account/expired'] 		= 'admin/account/expired';
$route['account/changepass'] 	= 'admin/account/changepass';

# Admin
$route['admin'] 				= 'admin';
$route['admin/source'] 			= 'admin/source';
$route['admin/source/add'] 		= 'admin/source/detail';
$route['admin/source/onoff'] 	= 'admin/source/onoff';

$route['admin/upload'] 			= 'admin/upload';
$route['admin/upload/file'] 	= 'admin/upload/dowloadfile';
$route['admin/upload/add'] 		= 'admin/upload/detail';

$route['admin/center'] 			= 'admin/center';
$route['admin/center/add'] 		= 'admin/center/detail';
$route['admin/center/edit/(:num)'] = 'admin/center/detail/$1';
$route['admin/center/onoff']	= 'admin/center/onoff';

$route['admin/department'] 		= 'admin/department';
$route['admin/department/add'] 	= 'admin/department/detail';
$route['admin/department/edit/(:num)'] = 'admin/department/detail/$1';
$route['admin/department/onoff']= 'admin/department/onoff';

$route['admin/group'] 			= 'admin/group';
$route['admin/group/add'] 		= 'admin/group/detail';
$route['admin/group/edit/(:num)'] = 'admin/group/detail/$1';
$route['admin/group/onoff']		= 'admin/group/onoff';

$route['admin/account'] 		= 'admin/account';
$route['admin/account/active'] 	= 'admin/account/activeacc';
$route['admin/account/add'] 	= 'admin/account/detail';
$route['admin/account/edit/(:num)'] = 'admin/account/detail/$1';
$route['admin/account/addlist'] = 'admin/account/createlist';
$route['admin/account/file'] 	= 'admin/account/dowloadfile';
$route['admin/account/onoff']	= 'admin/account/onoff';

$route['admin/ajax/cebyci'] 	= 'admin/ajax/center_by_city';
$route['admin/ajax/debyce'] 	= 'admin/ajax/department_by_center';
$route['admin/ajax/grbyde'] 	= 'admin/ajax/group_by_department';

# Center
$route['center'] 				= 'center';
$route['center/customer']		= 'center/customer';
$route['center/customer/transaction'] = 'center/customer/transaction';
$route['center/customer/assign']= 'center/customer/assign';
$route['center/customer/acassign']= 'center/customer/acassign';
$route['center/customer/unassign']= 'center/customer/unassign';
$route['center/changeagent']	= 'center/changeagent';

$route['center/frametime'] 		= 'center/frametime';
$route['center/frametime/add'] 	= 'center/frametime/detail';
$route['center/frametime/edit/(:num)'] = 'center/frametime/detail/$1';
$route['center/frametime/onoff']= 'center/frametime/onoff';

$route['center/call']			= 'center/call';
$route['center/call/appointment']= 'center/call/appointment';
$route['center/call/exappointment']= 'center/call/exappointment';

$route['center/limit'] 			= 'center/limit';
$route['center/limit/add'] 		= 'center/limit/detail';
$route['center/limit/department'] = 'center/limit/department';
$route['center/limit/department/add'] = 'center/limit/depdetail';
$route['center/ajax/cebyci'] 	= 'center/ajax/center_by_city';
$route['center/ajax/debyce'] 	= 'center/ajax/department_by_center';
$route['center/ajax/grbyce'] 	= 'center/ajax/group_by_center';
$route['center/ajax/agbyce'] 	= 'center/ajax/agent_by_center';
$route['center/ajax/grbyde'] 	= 'center/ajax/group_by_department';
$route['center/ajax/agbyde'] 	= 'center/ajax/agent_by_department';
$route['center/ajax/agbygr'] 	= 'center/ajax/agent_by_group';
$route['center/ajax/fibyso'] 	= 'center/ajax/fileup_by_source';

# Data
$route['data'] 					= 'data';
$route['data/fileup'] 			= 'data/fileup';
$route['data/source'] 			= 'data/source';
$route['data/source/onoff'] 	= 'data/source/onoff';
$route['data/source/add'] 		= 'data/source/detail';
$route['data/customer'] 		= 'data/customer';

# Department
$route['department'] 			= 'department';
$route['department/account'] 	= 'department/account';
$route['department/customer/assign'] = 'department/customer/assign';
$route['department/customer/acassign'] = 'department/customer/acassign';

$route['department/call/appointment'] = 'department/call/appointment';
$route['department/call/exappointment'] = 'department/call/exappointment';

$route['department/record'] 		= 'department/record';
$route['department/record/detail'] 	= 'department/record/detail';
$route['department/record/dowload'] = 'department/record/dowload';
# Group
$route['group/account'] 		= 'group/account';
$route['group/callappointment/(:num)']= 'group/callappointment/$1';
$route['group/call/list'] 		= 'group/call';
$route['group/call/exlist'] 	= 'group/call/exlistcall';
$route['group/call/appointment'] = 'group/call/appointment';
$route['group/call/exappointment'] = 'group/call/exappointment';
$route['group/customer/assign'] = 'group/customer/assign';
$route['group/customer/acassign'] = 'group/customer/acassign';

$route['group/report'] 		= 'group/report';
$route['group/report/exstatistics'] = 'group/report/exstatistics';

$route['group/record'] 			= 'group/record';
$route['group/record/detail'] 	= 'group/record/detail';
$route['group/record/dowload'] 	= 'group/record/dowload';
# Staff
$route['staff/list'] 			= 'staff/listcall';
$route['staff/startcase'] 		= 'staff/startcase';
$route['staff/newcall/(:num)'] 	= 'staff/startcase/$1';
$route['staff/call'] 			= 'staff/call';
$route['staff/call/callback'] 	= 'staff/call/callback';
$route['staff/callback/(:num)'] = 'staff/callback/$1';
$route['staff/edit/(:num)'] 	= 'staff/edit/$1';
$route['staff/call/appointment'] = 'staff/call/appointment';
$route['staff/call/nostatus'] 	= 'staff/call/nostatus';
$route['staff/introduced/(:num)'] = 'staff/introduced/$1';
$route['staff/introducedetail/(:num)'] = 'staff/introducedetail/$1';
$route['staff/call/callappointment/(:num)']= 'staff/call/callappointment/$1';

$route['staff/getcallback'] 	= 'staff/getcallback';

$route['staff/dial/(:any)'] 	= 'staff/dial/dialcall/$1';

$route['staff/ajax/statuschild1'] = 'staff/ajax/statuschild1';
$route['staff/ajax/statuschild2'] = 'staff/ajax/statuschild2';
$route['staff/ajax/dibyci'] 	= 'staff/ajax/districtbycity';
$route['staff/ajax/cespabyci'] 	= 'staff/ajax/centerspabycity';
$route['staff/ajax/frameapp'] 	= 'staff/ajax/frametimeappointment';

# Operator
$route['operator'] 				= 'operator';
$route['operator/dowload'] 		= 'operator/dowload';
$route['operator/detail'] 		= 'operator/detail';

$route['operator/listcallout'] 	= 'operator/listcall/out';
$route['operator/listcallin'] 	= 'operator/listcall/in';
$route['operator/statistics'] 	= 'operator/statistics';

$route['operator/infordetail'] 	= 'operator/infordetail';
$route['operator/exoperator'] 	= 'operator/exoperator';
#Confirm
$route['confirm'] 				= 'confirm';
$route['confirm/appointment'] 	= 'confirm/appointment';
$route['confirm/appointment/call/(:num)'] = 'confirm/callapp/$1';

#Support
$route['support'] 				= 'support';
$route['support/account'] 		= 'support/account';
$route['support/account/active'] 	= 'support/account/activeacc';
$route['support/account/add'] 	= 'support/account/detail';
$route['support/account/edit/(:num)'] = 'support/account/detail/$1';
$route['support/account/addlist'] = 'support/account/createlist';
$route['support/account/file'] 	= 'support/account/dowloadfile';
$route['support/account/onoff']	= 'support/account/onoff';

# Record
$route['record/out'] 				= 'record/index/out';
$route['record/in'] 				= 'record/index/in';
$route['record/infordetail'] 		= 'record/infordetail';