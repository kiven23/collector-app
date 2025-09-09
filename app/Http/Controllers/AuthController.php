<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class AuthController extends Controller
{
  /**
   * Create a new AuthController instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('auth:api', ['except' => ['login']]);
  }

  /**
   * Get a JWT via given credentials.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function login()
  {
    $credentials = request(['email', 'password']);

    if (!$token = auth('api')->attempt($credentials)) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }

    return $this->respondWithToken($token);
  }

  /**
   * Get the authenticated User.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function me()
  {
    return response()->json(auth('api')->user());
  }
  public function roles()
  {
    $permission = [];
    $home = [
      'text' => 'Home',
      'icon' => 'home',
      'route' => '/',
    ];
    if (\Auth::user()->hasRole(['Power Interruption Admin', 'Super Admin'])) {
      $powerInterruption = [
        'text' => 'Power Interruption',
        'icon' => 'power',
        'route' => '/power-interruptions',
      ];
    }
    if (\Auth::user()->hasRole([
      'Pending Admin',
      'Pending User',
      'Super Admin'
    ])) {
      $pendingTransaction = [
        'text' => 'Pending Transaction',
        'icon' => 'account_tree',
        'route' => '/pending-transactions',
      ];
    }
    if (\Auth::user()->hasRole(['Super Admin'])) {
      $Administrative = [
        'text' => 'Administrative',
        'icon' => 'business_center',
        'subLinks' =>
        [
          0 =>
          [
            'text' => 'Users',
            'links' =>
            [
              0 =>
              [
                'text' => 'Management',
                'icon' => 'account_circle',
                'route' => '/users',
              ],
              1 =>
              [
                'text' => 'Employments',
                'icon' => 'assignment',
                'route' => '/user-employments',
              ],
              2 =>
              [
                'text' => 'Divisions',
                'icon' => 'portrait',
                'route' => '/divisions',
              ],
              3 =>
              [
                'text' => 'Departments',
                'icon' => 'portrait',
                'route' => '/departments',
              ],
              4 =>
              [
                'text' => 'Positions',
                'icon' => 'assignment_ind',
                'route' => '/positions',
              ],
            ],
          ],
          1 =>
          [
            'text' => 'Authorizations',
            'links' =>
            [
              0 =>
              [
                'text' => 'Roles',
                'icon' => 'verified_user',
                'route' => '/roles',
              ],
              1 =>
              [
                'text' => 'Permissions',
                'icon' => 'lock',
                'route' => '/permissions',
              ],
            ],
          ],
          2 =>
          [
            'text' => 'Branches',
            'links' =>
            [
              0 =>
              [
                'text' => 'Management',
                'icon' => 'store_mall_directory',
                'route' => '/branches',
              ],
              1 =>
              [
                'text' => 'Schedules',
                'icon' => 'access_time',
                'route' => '/branch-schedules',
              ],
              2 =>
              [
                'text' => 'Regions',
                'icon' => 'terrain',
                'route' => '/regions',
              ],
            ],
          ],
        ],
      ];
    }
    if (\Auth::user()->hasRole([
      'Service Call Admin',
      'Service Call Computerware Admin',
      'Service Call Connectivity Admin',
      'Service Call Admin', 'Computerware Tickets Admin', 'Super Admin'
    ])) {
      $Service_Call = [
        'text' => 'Service Call',
        'icon' => 'perm_phone_msg',
        'subLinks' =>
        [
          0 =>
          [
            'text' => 'Tickets',
            'links' =>
            [
              0 =>
              [
                'text' => 'Connectivity',
                'icon' => 'turned_in',
                'route' => '/connectivity-tickets',
              ],
              1 =>
              [
                'text' => 'Computerware',
                'icon' => 'turned_in',
                'route' => '/computerware-tickets',
              ],
            ],
          ],
          1 =>
          [
            'text' => 'Services',
            'links' =>
            [
              0 =>
              [
                'text' => 'Providers',
                'icon' => 'turned_in',
                'route' => '/service-providers',
              ],
              1 =>
              [
                'text' => 'Types',
                'icon' => 'turned_in',
                'route' => '/service-types',
              ],
              2 =>
              [
                'text' => 'Categories',
                'icon' => 'turned_in',
                'route' => '/service-categories',
              ],
            ],
          ],
          2 =>
          [
            'text' => 'Products',
            'links' =>
            [
              0 =>
              [
                'text' => 'Items',
                'icon' => 'turned_in',
                'route' => '/product-items',
              ],
              1 =>
              [
                'text' => 'Brands',
                'icon' => 'turned_in',
                'route' => '/product-brands',
              ],
              2 =>
              [
                'text' => 'Categories',
                'icon' => 'turned_in',
                'route' => '/product-categories',
              ],
            ],
          ],
        ],
      ];
    }
    $css_perm = [];

    if (\Auth::user()->hasRole(['Credit Standing Access'])) {
      $cportal = [
        'text' => 'Credit Standing',
        'links' =>
        [
          0 =>
          [
            'text' => 'Dashboard',
            'icon' => 'folder_shared',
            'route' => '/ccs/creditstanding/dashboard',
          ]
        ],
      ];
    }
    if (\Auth::user()->hasRole(['Aging Recon Access'])) {
      $recon = [
        'text' => 'Aging Reconciliation',
        'links' =>
        [
          0 =>
          [
            'text' => 'Recon/Disc',
            'icon' => 'folder_shared',
            'route' => '/ccs/reconciliation/dashboard',
          ]
        ],
      ];
    }
    if (\Auth::user()->hasRole(['Installment Due Access'])) {
      $installment = [
        'text' => 'Installment Due',
        'links' =>
        [
          0 =>
          [
            'text' => 'Incoming Payment',
            'icon' => 'folder_shared',
            'route' => '/ccs/installment/dashboard',
          ]
        ],
      ];
    }
    if (\Auth::user()->hasRole([
      'BlackListed Customer Portal Admin',
      'BlackListed Customer Portal User'
    ])) {
      $blc = [
        'text' => 'Black Listed Customer',
        'links' =>
        [
          0 =>
          [
            'text' => 'Main',
            'icon' => 'block',
            'route' => '/ccs/blacklisted/dashboard',
          ]
        ],
      ];
    }
    if (\Auth::user()->hasRole([
      'CDR Branch',
      'CDR Main Office'
    ])) {
      $cdr = [
        'text' => 'Customer Digitized Req',
        'links' =>
        [
          0 =>
          [
            'text' => 'CDR',
            'icon' => 'folder_shared',
            'route' => '/ccs/customerdigitized/dashboard',
          ]
        ],
      ];
    }
    if (\Auth::user()->hasRole([
      'Dunning Letter Branch',
      'Dunning Letter Admin'
    ])) {
      $dunning_letters = [
        'text' => 'Dunning Letters',
        'links' =>
        [
          0 =>
          [
            'text' => 'Dashboard',
            'icon' => 'description',
            'route' => '/ccs/dunning-letters',
          ]
        ],
      ];
    }
    array_push($css_perm, @$cportal, @$blc, @$cdr, @$installment, @$recon, @$dunning_letters);
    if (\Auth::user()->hasRole([
      'CDR Main Office',
      'CDR Branch',
      'BlackListed Customer Portal Admin',
      'BlackListed Customer Portal User',
      'Installment Due Access',
      'Aging Recon Access',
      'Credit Standing Access',
      'Dunning Letter Branch',
      'Dunning Letter Admin',
    ])) {
      $ccs = [
        'text' => 'Credit & Collection',
        'icon' => 'description',
        'subLinks' => array_filter($css_perm)
      ];
    }
    $data = [];
    if (\Auth::user()->hasRole([
      'Archived Admin',
      'Archived User'
    ])) {
      $dataArchived =
        [
          'text' => 'Archived',
          'icon' => 'library_books',
          'route' => '/archived',
        ];
    }
    if (\Auth::user()->hasRole([
      'Agencies Admin User',
      'Agencies Branch User',
      'Agencies Guest User'
    ])) {
      $dataAgency = [
        'text' => 'Agencies',
        'icon' => 'file_download',
        'route' => '/agencies',
      ];
    }
    array_push($data, @$dataArchived, @$dataAgency);
    if (\Auth::user()->hasRole([
      'Archived Admin',
      'Archived User', 'Agencies Admin User',
      'Agencies Branch User',
      'Agencies Guest User'
    ])) {
      $govengency = [
        'text' => 'Gov\'t. Agency',
        'icon' => 'groups',
        'subLinks' =>
        [
          0 =>
          [
            'text' => 'Reports',
            'links' => array_filter($data),
          ],
        ]
      ];
    }

    if (\Auth::user()->hasRole(['Validation Portal'])) {
      $validation_portal = [
        'text' => 'Validation Portal',
        'icon' => 'mdi-file-check-outline',
        'subLinks' =>
        [
          0 =>
          [
            'text' => 'Template',
            'links' =>
            [
              0 =>
              [
                'text' => 'Dashboard',
                'icon' => 'description',
                'route' => '/validation-portal/template',
              ]
            ],
          ],
          1 =>
          [
            'text' => 'Good Receipt Model - Serial Model',
            'links' => [
              0 => [
                'text' => 'Dashboard',
                'icon' => 'description',
                'route' => '/validation-portal/good-receipt-model-serial-model',
              ]
            ]
          ],
          2 =>
          [
            'text' => 'BP Master Data CardCode - AR Invoice CardCode',
            'links' => [
              0 => [
                'text' => 'Dashboard',
                'icon' => 'description',
                'route' => '/validation-portal/bp-master-cardcode-ar-invoice-cardcode',
              ]
            ]
          ],
        ],
      ];
    }

    $revolving_fund = [
      'text' => 'Revolving Fund',
      'icon' => 'mdi-cash-check',
      'route' => '/revolving-fund'
    ];

    $smsystem = [];
    if (\Auth::user()->hasRole(['Gift Code Terminal'])) {
      $giftcodes = [
        'text' => 'HBD Gift Codes',
        'icon' => 'search',
        'route' => '/giftcodes/index',
      ];
      $raffle = [
        'text' => 'Raffle Draw',
        'icon' => 'mdi-cash-check',
        'route' => '/raffle',
      ];
    }
    array_push($smsystem, @$giftcodes, @$raffle);
    if (\Auth::user()->hasRole(['Gift Code Terminal'])) {
      $sms = [
        'text' => 'SMS SYSTEM',
        'icon' => 'file_download',
        'subLinks' =>
        [
          0 =>
          [
            'text' => 'Automated Gift Code',
            'links' => array_filter($smsystem),
          ],
        ]
      ];
    }

    $sapb1Reports = [];
     if (\Auth::user()->hasRole(['SapApiAccess'])) {
      $incomingPaymentcrb = [
        'text' => 'Incoming Payments CRB',
        'icon' => 'description',
        'route' => '/sapb1/reports/index',
      ];
      $q1 = [
        'text' => 'Invoice Query Series Revised',
        'icon' => 'description',
        'route' => '/sapb1/reports/query/series/revised',
      ];
      $q2 = [
        'text' => 'Marketing AR Invoice',
        'icon' => 'description',
        'route' => '/sapb1/reports/query/marketing/ar/invoice',
      ];
      $q3 = [
        'text' => 'Summary of Customer Deposit Applied',
        'icon' => 'description',
        'route' => '/sapb1/reports/query/summary/customer/depositapplied',
      ];
      $q4 = [
        'text' => 'Adjustment Sales Discount',
        'icon' => 'description',
        'route' => '/sapb1/reports/query/adjustment/sales/discount',
      ];
      $q5 = [
        'text' => 'Recomputed Account',
        'icon' => 'description',
        'route' => '/sapb1/reports/query/recomputed/account',
      ];
      $q6 = [
        'text' => 'Searching of vehicles parts',
        'icon' => 'description',
        'route' => '/sapb1/reports/query/searching/vehicles/parts',
      ];
      $q7 = [
        'text' => 'AR Invoice Open Balance',
        'icon' => 'description',
        'route' => '/sapb1/reports/ar/openbalance',
      ];
      $q8 = [
        'text' => 'Incoming Payment Customer Deposit',
        'icon' => 'description',
        'route' => '/sapb1/reports/incomingpayment/customerdeposit',
      ];
      $q9 = [
        'text' => 'Incoming Payment Open Balance',
        'icon' => 'description',
        'route' => '/sapb1/reports/incomingpayment/openbalance',
      ];
     }
    array_push($sapb1Reports,  @$incomingPaymentcrb,  @$q1,@$q2,@$q3,@$q4,@$q5,@$q6,@$q7,@$q8,@$q9);
    if (\Auth::user()->hasRole(['SapApiAccess'])) {
      $sapreports = [
        'text' => 'SAP B1 REPORTS',
        'icon' => 'description',
        'subLinks' =>
        [
          0 =>
          [
            'text' => 'Sap Queries',
            'links' => array_filter($sapb1Reports),
          ],
        ]
      ];
      }
      $sapcon = [];
       if (\Auth::user()->hasRole(['Database Administrator'])) {
        $databases = [
          'text' => 'Configure',
          'icon' => 'settings',
          'route' => '/settings/database/configure',
        ];
       
        }
      array_push($sapcon , @$databases);
       if (\Auth::user()->hasRole(['Database Administrator'])) {
        $settings = [
          'text' => 'SETTINGS',
          'icon' => 'settings',
          'subLinks' =>
          [
            0 =>
            [
              'text' => 'Database Connection',
              'links' => array_filter($sapcon),
            ],
          ]
        ];
     }
     $creditinvestigation = [];
       if (\Auth::user()->hasRole(['Super Admin'])) {
        $credit = [
          'text' => 'Credit Investigation',
          'icon' => 'settings',
          'route' => '/smart_tools/credit/investigation',
        ];
       
        }
      array_push($creditinvestigation , @$credit );
       if (\Auth::user()->hasRole(['Super Admin'])) {
        $creditsmart = [
          'text' => 'SMART TOOLS',
          'icon' => 'settings',
          'subLinks' =>
          [
            0 =>
            [
              'text' => 'AI',
              'links' => array_filter($creditinvestigation),
            ],
          ]
        ];
     }
     if (\Auth::user()->hasRole(['Super Admin'])) {
        $collection = [
          'text' => 'Dashboard',
          'icon' => 'mdi-view-dashboard',
          'route' => '/collection/index/',
        ];
     }
      if (\Auth::user()->hasRole(['Super Admin'])) {
        $collected = [
          'text' => 'Collected',
          'icon' => 'mdi-check-circle-outline',
          'route' => '/collection/collected/',
        ];
      }
  
    //@$creditsmart
    array_push($permission, @$Administrative,  @$settings, @$collection,@$collected)  ;

    return array_filter($permission);
  }

  public function permission()
  {
    return $permission = \Auth::user()->getAllPermissions()->pluck('name');
  }
  /**
   * Log the user out (Invalidate the token).
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function logout()
  {
    auth('api')->logout();

    return response()->json(['message' => 'Successfully logged out']);
  }

  /**
   * Refresh a token.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function refresh()
  {
    return $this->respondWithToken(auth('api')->refresh());
  }

  /**
   * Get the token array structure.
   *
   * @param  string $token
   *
   * @return \Illuminate\Http\JsonResponse
   */
  protected function respondWithToken($token)
  {
    $user = $this->guard()->user();
    return response()->json([
      'access_token' => $token,
      'user' => $user->select(
        'id',
        'branch_id',
        'first_name',
        'last_name',
        \DB::raw("CONCAT(first_name,' ',last_name) AS full_name")
      )
        ->with('branch')
        ->with(['employment' => function ($qry) {
          $qry->with('position')->with('department');
        }])
        ->where('id', $user->id)
        ->first(),
      'token_type' => 'bearer',
      'expires_in' => auth('api')->factory()->getTTL() * 300
    ]);
  }

  public function guard()
  {
    return \Auth::Guard('api');
  }
}
