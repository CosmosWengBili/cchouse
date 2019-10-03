<?php
return [
    'User' => [
        'id' => '編號',
        'name' => '姓名',
        'email' => '電子郵件',
        'mobile' => '聯絡電話',
        'password' => '密碼',
        'created_at' => '建立時間',
        'updated_at' => '更新時間',
    ],
    'Audit' => [
        'id' => '編號',
        'user_type' => 'User Model',
        'user_id' => 'User編號',
        'event' => '事件',
        'auditable_type' => 'Model',
        'auditable_id' => 'Model編號',
        'old_values' => '變更前資料',
        'new_values' => '變更後資料',
        'url' => '操作網址',
        'ip_address' => 'IP Address',
        'user_agent' => 'User Agent',
        'tags' => '標籤',
        'created_at' => '建立時間',
        'updated_at' => '更新時間',
    ],
    'Tenant' => [
        'id' => '編號',
        'name' => '姓名',
        'certificate_number' => '證號',
        'is_legal_person' => '是否法人',
        'line_id' => 'Line編號',
        'residence_address' => '戶籍地址',
        'company' => '任職公司',
        'job_position' => '任職職位',
        'company_address' => '任職公司地址',
        'birth' => '出生年月日',
        'confirm_by' => '資料確認人員',
        'confirm_at' => '資料確認時間',
        'created_at' => '建立時間',
        'updated_at' => '更新時間',
        'emergency_contacts' => '緊急聯絡人',
        'guarantors' => '保證人',
        'contact_infos' => '聯絡資料',
        'tenant_contracts' => '租客合約',
        'residence_address' => '戶籍地址',
        'mailing_address' => '聯絡地址',
        'phone' => '電話',
        'email' => '電子郵件',
        'fax_number' => '傳真',
    ],
    'Landlord' => [
        'id' => '編號',
        'name' => '姓名',
        'certificate_number' => '身份字號/統編',
        'is_legal_person' => '是否法人',
        'birth' => '出生年月日',
        'note' => '備註',
        'is_collected_by_third_party' => '是否第三人代收',
        'landlord_contracts' => '房東合約',
        'agents' => '代理人',
        'contact_infos'=> '聯絡資料',
        'residence_address' => '戶籍地址',
        'mailing_address' => '聯絡地址',
        'phone' => '電話',
        'email' => '電子郵件',
        'fax_number' => '傳真',
        'bank_code' => '匯款銀行',
        'branch_code' => '匯款分行',
        'account_name' => '戶名',
        'account_number' => '帳號',
        'invoice_collection_method' => '發票領取方式',
        'invoice_collection_number' => '發票領取號碼',
        'invoice_mailing_address' => '發票寄送地址',
        'documents' => '相關文件',
        'created_at' => '建立時間',
        'updated_at' => '更新時間',
    ],
    'LandlordContract' => [
        'id' => '編號',
        'commission_type' => '承租方式',
        'commission_start_date' => '委託起',
        'commission_end_date' => '委託迄',
        'warranty_start_date' => '保固起',
        'warranty_end_date' => '保固迄',
        'rental_decoration_free_start_date' => '免租金裝潢起',
        'rental_decoration_free_end_date' => '免租金裝潢迄',
        'annual_service_fee_month_count' => '年繳服務費月數',
        'charter_fee' => '包租費用',
        'taxable_charter_fee' => '包租報稅費用',
        'agency_service_fee' => '仲介服務費',
        'rent_collection_frequency' => '收租頻率',
        'rent_collection_time' => '收租時間',
        'rent_adjusted_date' => '租金調整日',
        'adjust_ratio' => '調整金額',
        'deposit_month_count' => '押金月數',
        'is_collected_by_third_party' => '是否代收',
        'is_notarized' => '是否公證',
        'commissioner_id' => '專員',
        'landlords'=> '房東',
        'building_id' => '物件編號',
        'building'=> '建物',
        'buildings'=> '建物',
        'documents' => '相關文件',
        'landlord_ids' => '房東編號',
        'created_at' => '建立時間',
        'updated_at' => '更新時間',
        'can_keep_pets' => '養寵物',
        'gender_limit' => '性別限制',
        'commissioner' => '專員姓名',
        'building_code' => '物件代碼',
        'building_title' => '簡稱',
        'building_location' => '地址',
        'room_number' => '房號',
        'room_status' => '房狀態',
    ],
    'LandlordPayment' => [
        'id' => '編號',
        'amount' => '費用',
        'subject' => '科目',
        'bill_start_date' => '帳單期初',
        'bill_end_date' => '帳單期末',
        'bill_serial_number' => '帳單號',
        'billing_vendor' => '廠商',
        'collection_date' => '繳費日',
        'comment' => '備註',
        'room_id' => '房編號',
        'buildings' => '建物',
        'rooms' => '房',
        'created_at' => '建立時間',
        'updated_at' => '更新時間',
        'building_code' => '物件代碼',
        'building_title' => '簡稱',
        'building_location' => '地址',
        'room_number' => '房號',
        'room_status' => '房狀態',
        'commission_type' => '承租方式',
    ],
    'Key' => [
        'id' => '編號',
        'key_name' => '鑰匙代號',
        'room_id' => '房代號',
        'keeper_id' => '保管人',
        'users' => '保管人',
        'rooms' => '房',
        'key_requests' => '鑰匙紀錄',
        'created_at' => '建立時間',
        'updated_at' => '更新時間',
        'building_code' => '物件代碼',
        'building_title' => '簡稱',
        'building_location' => '地址',
        'room_number' => '房號',
        'room_status' => '房狀態',
        'commission_type' => '承租方式',
        'comment' => '備註',
        'scrap_date' => '報廢日期',
        'keepers' => '保管人',
        'is_scraped' => '是否已報廢',
    ],
    'KeyRequest' => [
        'id' => '編號',
        'request_user_id' => '借用人',
        'status' => '狀態',
        'request_date' => '出借日',
        'request_approved' => '出借允許',
        'key_id' => '鑰匙編號',
        'created_at' => '建立時間',
        'updated_at' => '更新時間',
        'borrow_date' => '預計借日',
        'return_date' => '預計還日',
        'comment' => '備註',
    ],
    'Guarantor' => [
        'id' => '編號',
        'name' => '姓名',
        'phone' => '電話',
        'relationship' => '關係',
        'created_at' => '建立時間',
        'updated_at' => '更新時間',
    ],
    'EmergencyContact' => [
        'id' => '編號',
        'name' => '姓名',
        'phone' => '電話',
        'relationship' => '關係',
        'created_at' => '建立時間',
        'updated_at' => '更新時間',
    ],
    'Agent' => [
        'id' => '編號',
        'landlord_id' => '房東編號',
        'name' => '姓名',
        'certificate_number' => '證號',
        'phone' => '聯絡電話',
        'mailing_address' => '聯絡地址',
        'email' => '電子郵件',
        'created_at' => '建立時間',
        'updated_at' => '更新時間',
    ],
    'ContactInfo' => [
        'id' => '編號',
        'info_type' => '類別',
        'value' => '資料',
        'created_at' => '建立時間',
        'updated_at' => '更新時間',
    ],
    'Maintenance' => [
        'id' => '編號',
        'tenant_contract_id' => '租客合約編號',
        'reported_at' => '反映日期',
        'expected_service_date' => '預計處理日期',
        'expected_service_time' => '預計處理時間',
        'dispatch_date' => '派工日',
        'commissioner_id' => '報修人員',
        'maintenance_staff_id' => '維修人員',
        'closed_date' => '結案日期',
        'closed_comment' => '完工備註',
        'service_comment' => '房客備註',
        'status' => '狀態',
        'incident_details' => '事故說明',
        'incident_type' => '事故類別',
        'work_type' => '工種',
        'number_of_times' => '趟數',
        'payment_request_date' => '請款日期',
        'closing_serial_number' => '結案單號',
        'billing_details' => '登帳說明',
        'payment_request_serial_number' => '請款單號',
        'cost' => '成本',
        'price' => '複價',
        'is_recorded' => '是否入帳',
        'comment' => '備註',
        'created_at' => '建立時間',
        'tenant_contract' => '租客合約',
        'room' => '房間',
        'tenant' => '租客',
        'updated_at' => '更新時間',
        'building_code' => '物件代碼',
        'building_title' => '簡稱',
        'building_location' => '地址',
        'room_number' => '房號',
        'room_status' => '房狀態',
        'commission_type' => '承租方式',
    ],
    'Appliance' => [
        'id' => '',
        'room_id' => '室編號',
        'subject' => '項目',
        'spec_code' => '型號',
        'vendor' => '廠商',
        'count' => '個數',
        'maintenance_phone' => '維護電話',
        'comment' => '備註',
        'created_at' => '建立時間',
        'updated_at' => '更新時間'
    ],
    'Building' => [
        'id' => '編號',
        'title' => '簡稱',
        'city' => '縣市',
        'district' => '區域',
        'address' => '地址',
        'tax_number' => '稅籍編號',
        'building_type' => '物件類型',
        'floor' => '樓層',
        'legal_usage' => '法定用途',
        'has_elevator' => '電梯',
        'security_guard' => '管理室和管理員',
        'management_count' => '管理件數',
        'first_floor_door_opening' => '一樓大門開門方式',
        'public_area_door_opening' => '各樓層公區開門方式',
        'room_door_opening' => '臥室門開門方式',
        'main_ammeter_location' => '台電總電表位址',
        'ammeter_serial_number_1' => '台電電號1( 可能多個 )',
        'shared_electricity' => '公電',
        'electricity_payment_method' => '台電帳單付款方式',
        'private_ammeter_location' => '自設分電表位置',
        'water_meter_location' => '自來水表位置',
        'water_meter_serial_number' => '自來水表表號',
        'water_payment_method' => '自來水帳單付款方式',
        'water_meter_reading_date' => '水錶抄表日期',
        'gas_meter_location' => '天然氣表位置',
        'garbage_collection_location' => '收垃圾地點',
        'garbage_collection_time' => '收垃圾時間',
        'management_fee_payment_method' => '管理費繳費方式',
        'management_fee_contact' => '管理費聯絡人',
        'management_fee_contact_phone' => '管理費聯絡電話',
        'distribution_method' => '分配方式',
        'administrative_number' => '行政區碼',
        'accounting_group' => '會計組別',
        'rental_receipt' => '租金收據',
        'commissioner_id' => '招租人員',
        'administrator_id' => '管理人員',
        'comment' => '備註',
        'carry' => '沖銷餘額',
        'location' => '完整地址',
        'landlord_contract_id' => '房東合約編號',
        'rooms' => '房',
        'landlordContracts' => '房東合約',
        'created_at' => '建立時間',
        'updated_at' => '更新時間',
        'building_code' => '物件代碼',
        'room_number' => '房號',
        'room_status' => '房狀態',
        'commission_type' => '承租方式',
    ],
    'Room' => [
        'id' => '編號',
        'building_id' => '物件編號',
        'needs_decoration' => '是否需裝修',
        'room_code' => '物件代碼',
        'virtual_account' => '虛擬帳號',
        'room_status' => '狀態',
        'room_number' => '房號',
        'room_layout' => '物件格局',
        'room_attribute' => '物件屬性',
        'living_room_count' => '客餐廳',
        'room_count' => '房間',
        'bathroom_count' => '衛浴',
        'parking_count' => '車位',
        'ammeter_reading_date' => '電表抄表日期',
        'rent_list_price' => '租金牌價',
        'rent_reserve_price' => '租金底價',
        'rent_landlord' => '房東租金',
        'rent_actual' => '實際租金',
        'internet_form' => '網路形式',
        'management_fee_mode' => '管理費模式',
        'management_fee' => '管理費',
        'wifi_account' => 'Wifi',
        'wifi_password' => 'Wifi',
        'has_digital_tv' => '數位電視',
        'comment' => '備註',
        'tenant_contracts' => '租客合約',
        'keys' => '鑰匙',
        'appliances' => '附屬設備',
        'landlord_payments' => '房東應付帳單',
        'landlord_other_subjects' => '房東其他科目',
        'created_at' => '建立時間',
        'updated_at' => '更新時間'
    ],
    'TenantContract' => [
        'id' => '編號',
        'room_id' => '室編號',
        'tenant_id' => '租客編號',
        'contract_serial_number' => '契約序號',
        'set_other_rights' => '設定他項權利',
        'other_rights' => '他項權利種類',
        'sealed_registered' => '查封登記',
        'car_parking_floor' => '汽車停車層',
        'car_parking_type' => '汽車停車種類',
        'car_parking_space_number' => '汽車停車編號',
        'motorcycle_parking_floor' => '機車停車層',
        'motorcycle_parking_space_number' => '機車停車編號',
        'motorcycle_parking_count' => '機車停車個數',
        'effective' => '是否已生效',
        'contract_start' => '租約起',
        'contract_end' => '租約迄',
        'rent' => '租金',
        'rent_pay_day' => '租金支付日',
        'deposit' => '押金',
        'deposit_paid' => '押金已繳納',
        'electricity_payment_method' => '電費繳款方式',
        'electricity_calculate_method' => '電費計算方式',
        'electricity_price_per_degree' => '電費費率',
        'electricity_price_per_degree_summer' => '電費夏季費率',
        '110v_start_degree' => '110v 起度',
        '220v_start_degree' => '220v 起度',
        '110v_end_degree' => '110v 結度',
        '220v_end_degree' => '220v 結度',
        'invoice_collection_method' => '發票領取方式',
        'invoice_collection_number' => '發票領取號碼',
        'created_at' => '建立時間',
        'updated_at' => '更新時間',
        'commissioner_id' => '專員編號',
        'currentBalance' => '目前餘額',
        'building' => '物件',
        'room' => '房',
        'tenant_payments' => '租客帳單',
        'tenant_electricity_payments' => '租客電費',
        'debt_collections' => '催收',
        'pay_logs' => '繳款紀錄',
        'deposits' => '訂金',
        'maintenances' => '清潔維修',
        'tenant' => '租客',
        'company_incomes' => '公司收入',
        'building_code' => '物件代碼',
        'building_title' => '簡稱',
        'building_location' => '地址',
        'room_number' => '房號',
        'room_status' => '房狀態',
        'commission_type' => '承租方式',
        'sum_paid' => '已繳總額',
        "comment" => "備註",
    ],
    'DebtCollection' => [
        'id' => '編號',
        'collector_id' => '催收人編號',
        'tenant_contract_id' => '租客合約編號',
        'details' => '催收說明',
        'status' => '催收狀態',
        'is_penalty_collected' => '是否收滯納金',
        'comment' => '備註',
        'created_at' => '建立時間',
        'updated_at' => '更新時間',
        'received_at' => '收取時間',
        'building_code' => '物件代碼',
        'building_title' => '簡稱',
        'building_location' => '地址',
        'room_number' => '房號',
        'room_status' => '房狀態',
        'commission_type' => '承租方式',
    ],
    'Shareholder' => [
        'id' => '編號',
        'name' => '姓名',
        'email' => 'email',
        'bank_name' => '銀行名稱',
        'bank_code'=> '銀行代碼',
        'account_number' => '銀行號碼',
        'account_name' => '銀行戶名',
        'is_remittance_fee_collected' => '是否收取匯費',
        'transfer_from' => '匯出銀行',
        'bill_delivery' => '帳單寄送方式',
        'distribution_method' => '分配方式',
        'distribution_start_date' => '分配起',
        'distribution_end_date' => '分配迄',
        'distribution_rate' => '分配費率',
        'investment_amount' => '投資額',
        'buildings' => '物件',
        'building_ids' => '物件編號',
        'created_at' => '建立時間',
        'updated_at' => '更新時間'
    ],
    'Deposit' => [
        'id' => '編號',
        'tenant_contracts' => '租客合約',
        'tenant_contract_id' => '租客合約編號',
        'deposit_collection_date' => '收訂日期',
        'deposit_collection_serial_number' => '收訂單號',
        'deposit_confiscated_amount' => '沒定金額',
        'deposit_returned_amount' => '退訂金額',
        'confiscated_or_returned_date' => '沒/退訂日期',
        'invoicing_amount' => '應開立金額',
        'invoice_date' => '發票日期',
        'is_deposit_collected' => '已收訂',
        'comment' => '備註',
        'tenantContracts' => '租客合約',
        'rooms' => '房',
        'buildings' => '物件',
        'created_at' => '建立時間',
        'updated_at' => '更新時間',
        'building_code' => '物件代碼',
        'building_title' => '簡稱',
        'building_location' => '地址',
        'room_number' => '房號',
        'room_status' => '房狀態',
        'commission_type' => '承租方式',
    ],
    'TenantPayment' => [
        "id" => '編號',
        "tenant_contract_id" => "租客合約編號",
        "subject" => "科目",
        "due_time" => "應繳時間",
        "amount" => "費用",
        "is_charge_off_done" => "是否已沖銷",
        "charge_off_date" => "沖銷日期",
        "invoice_serial_number" => "發票號碼",
        "collected_by" => "收取者",
        "is_visible_at_report" => "是否顯示在報表",
        "is_pay_off" => "是否為點交",
        "period" => '期數',
        "comment" => "備註",
        'created_at' => '建立時間',
        'updated_at' => '更新時間'
    ],
    'TenantElectricityPayment' => [
        "id" => '編號',
        "subject" => '科目',
        "tenant_contract_id" => "租客合約編號",
        "ammeter_read_date" => "抄表時間",
        "110v_start_degree" => "110v起",
        "110v_end_degree" => "110v迄",
        "220v_start_degree" => "220v起",
        "220v_end_degree" => "220v迄",
        "amount" => "費用",
        "invoice_serial_number" => "發票號碼",
        "is_charge_off_done" => "是否已沖銷",
        "due_time" => '應繳時間',
        "comment" => "備註",
        'created_at' => '建立時間',
        'updated_at' => '更新時間'
    ],
    'CompanyIncome' => [
        'company_incomes' => 'Company Incomes',
        "id" => '編號',
        "tenant_contract_id" => "租客合約編號",
        "subject" => "項目",
        "income_date" => "收入時間",
        "amount" => "費用",
        "comment" => "備註",
        'created_at' => '建立時間',
        'updated_at' => '更新時間',
        'building_code' => '物件代碼',
        'building_title' => '簡稱',
        'building_location' => '地址',
        'room_number' => '房號',
        'room_status' => '房狀態',
        'commission_type' => '承租方式',
    ],
    'PayLog' => [
        "id" => '編號',
        "loggable_type" => "紀錄類型",
        "loggable_id" => "紀錄編號",
        "subject" => "科目",
        "payment_type" => "繳費類別",
        "amount" => "費用",
        "virtual_account" => "虛擬帳號",
        "paid_at" => "匯款時間",
        "tenant_contract_id" => "租客合約編號",
        'created_at' => '建立時間',
        'updated_at' => '更新時間'
    ]
];
