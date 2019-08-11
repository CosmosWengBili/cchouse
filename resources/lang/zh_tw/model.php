<?php
return [
    'User' => [
        'id' => '編號',
        'name' => '姓名',
        'email' => '電子郵件',
        'mobile' => '聯絡電話',
        'password' => '密碼'
    ],
    'Audit' => [
        'id' => '編號',
        'user_type' => 'User Model',
        'user_id' => 'User ID',
        'event' => '事件',
        'auditable_type' => 'Model',
        'auditable_id' => 'Model ID',
        'old_values' => '變更前資料',
        'new_values' => '變更後資料',
        'url' => '操作網址',
        'ip_address' => 'IP Address',
        'user_agent' => 'User Agent',
        'tags' => '標籤',
        'created_at' => '發生時間',
    ],
    'Tenant' => [
        'id' => '編號',
        'name' => '姓名',
        'certificate_number' => '證號',
        'is_legal_person' => '是否法人',
        'line_id' => 'Line ID',
        'residence_address' => '戶籍地址',
        'company' => '任職公司',
        'job_position' => '任職職位',
        'company_address' => '任職公司地址',
        'confirm_by' => '資料確認人員',
        'confirm_at' => '資料確認時間',
        'created_at' => '建立時間',
        'updated_at' => '最後更新時間',
        'emergency_contacts' => '緊急聯絡人',
        'guarantors' => '保證人',
        'contact_infos' => '聯絡資料',
        'tenant_contracts' => '租客合約',
        'residence_address' => '戶籍地址',
        'mailing_address' => '聯絡地址',
        'phone' => '電話',
        'email' => '電子郵件',
        'fax_number' => '傳真'
    ],
    'Landlord' => [
        'id' => '編號',
        'name' => '姓名',
        'certificate_number' => '證號',
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
        'fax_number' => '傳真'
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
        'adjust_ratio' => '調整 % 數',
        'deposit_month_count' => '押金月數',
        'is_collected_by_third_party' => '是否代收',
        'is_notarized' => '是否公證',
        'bank_code' => '匯款銀行',
        'branch_code' => '匯款分行',
        'account_name' => '戶名',
        'account_number' => '帳號',
        'invoice_collection_method' => '發票領取方式',
        'invoice_collection_number' => '發票領取號碼',
        'invoice_mailing_address' => '發票寄送地址',
        'commissioner_id' => '專員',
        'landlord_id' => '房東',
        'landlords'=> '房東',
        'building_id' => '建物',
        'buildings'=> '建物',

    ],
    'Key' => [
        'id' => '編號',
        'key_name' => '鑰匙代號',
        'room_id' => '相對應房',
        'keeper_id' => '保管人',
        'users' => '保管人',
        'rooms' => '房',
        'key_requests' => '鑰匙紀錄'
    ],
    'KeyRequest' => [
        'id' => '編號',
        'request_user_id' => '借用人',
        'status' => '狀態',
        'request_date' => '出借日',
        'request_approved' => '出借允許',
        'key_id' => '鑰匙編號'
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
        'residence_address' => '戶籍地址',
        'mailing_address' => '聯絡地址',
        'email' => '電子郵件'

    ],
    'ContactInfo' => [
        'id' => '編號',
        'info_type' => '類別',
        'value' => '資料'
    ],
    'Maintenance' => [
        'id' => '編號',
        'tenant_contract_id' => '租客合約 ID',
        'reported_at' => '反映日期',
        'expected_service_date' => '預計處理日期',
        'expected_service_time' => '預計處理時間',
        'dispatch_date' => '派工日',
        'commissioner_id' => '處理專員',
        'maintenance_staff_id' => '維修人員',
        'closed_date' => '結案日期',
        'closed_comment' => '完工備註',
        'service_comment' => '處理備註',
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
        'invoice_serail_number' => '發票號碼',
        'comment' => '備註',
        'created_at' => '建立時間',
        'tenant_contract' => '租客合約',
        'room' => '房間',
        'tenant' => '租客',
    ],
    'Appliance' => [
        'id' => '',
        'room_id' => '室ID',
        'subject' => '項目',
        'spec_code' => '型號',
        'vendor' => '廠商',
        'count' => '個數',
        'maintenance_phone' => '維護電話',
        'comment' => '備註',
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
        'landlord_contract_id' => '房東合約 ID',
        'rooms' => '房',
        'landlordContracts' => '房東合約'
    ],
    'Room' => [
        'id' => '編號',
        'building_id' => '物件 ID',
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
        'can_keep_pets' => '養寵物',
        'gender_limit' => '性別限制',
        'comment' => '備註',
    ],
    'TenantContract' => [
        'id' => '編號',
        'room_id' => '室ID',
        'tenant_id' => '租客ID',
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
        'commissioner_id' => '專員 ID',
    ]
];
