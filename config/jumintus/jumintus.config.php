<?php
  return [
     # Project configs...

    'version' => '0.2.0',

    'project' => 'jumintus',
    'company' => 'edcaraujo.com',

    'title' => 'jumintus',
    'description' => 'Haiô Silver! Sistema de Helpdesk Go Horse (com Asana).',
    'icon' => '<i class="fas fa-horse"></i>',
    'url' => 'http://localhost/jumintus/',

    'test' => true,

    # Components configs...

    'auth' => [ 
      'jumintus-data'
    ],

    # Rules configs...

    // Rules are analyzed top-down and it is a tool to completely 
    // change the properties of the task. If you don't know what 
    // you're doing, don't touch this section!
    //
    // Rules are specified as the following list:
    //
    //  [ 
    //    1. task-timestamp (gte), // Filter
    //    2. task-timestamp (lt),  // Filter
    //    3. auth-user,            // Filter
    //    4. user-profile,         // Filter
    //    5. user-role,            // Filter
    //    6. user-authorization    // Filter
    //    7. task-category,        // Filter
    //    8. task-source,          // Filter
    //    9. task-responsable,     // Filter
    //    10. task-schedule,        // Filter
    //    11. task-priority,        // Filter
    //    12. task-project,         // Filter
    //    13. local-label,          // Filter
    //    14. local-unity,          // Filter
    //    15. local-departament,    // Filter
    //    16. <PROPERTY>,           // <PROPERTY> to change. See task array in 'haiosilver.php'.
    //    17. <VALUE>               // <VALUE> to be assign.
    //  ]
    //
    // ['*','*','*','*','*','*','*','*','*','*','*','*','*','*','*','<PROPERTY>','<VALUE>']

    'rules' => [
      ['*','*','*','*','*','*','*','*','*','*','*','*','*','*','*','task-responsable','systallone'],
      ['*','*','*','*','*','*','*','*','*','*','*','*','*','yul','*','task-responsable','tecrews'],
      ['*','*','*','*','*','*','*','*','*','*','*','*','*','pgp','*','task-responsable','jeli'],

      ['*','*','*','*','*','*','*','*','*','*','*','*','*','*','*','task-priority','!'],
      ['*','*','systallone','*','*','*','*','*','*','*','*','*','*','*','*','task-priority','-'],
      ['*','*','tecrews','*','*','*','*','*','*','*','*','*','*','*','*','task-priority','-'],
      ['*','*','jeli','*','*','*','*','*','*','*','*','*','*','*','*','task-priority','-'],

      ['*','*','*','*','*','*','*','*','*','*','*','*','*','*','*','task-project','123456789'],
      ['*','*','*','*','*','*','per','*','*','*','*','*','*','*','*','task-project','987654321'],
      ['*','*','jeli','*','*','*','*','*','*','*','*','*','*','*','*','task-project','987654321'],
      
      ['*','*','*','*','*','*','*','*','*','*','*','*','*','*','*','task-authorization','0'],

      ['*','*','*','*','*','*','*','*','*','*','*','*','*','*','*','task-source','sis'],
      ['*','*','*','*','*','*','*','*','*','*','*','*','*','*','*','task-schedule','ext'],

      ['2021-02-17 08:00','2021-02-18 01:00','*','*','*','*','*','*','*','*','*','*','*','yul','*','task-warnings','A Unidade \'Base naval de Yulin (YUL)\' não terá atendimento hoje.'],
    ],
  ];
?>