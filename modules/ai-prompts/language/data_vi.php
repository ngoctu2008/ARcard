<?php

/**
 * @Project NUKEVIET 4.x
 * @Author AIPrompts Team
 * @Copyright (C) 2024. All rights reserved
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE')) {
    die('Stop!!!');
}

// Ensure categories exist
$sql_cat = "INSERT INTO `" . NV_PREFIXLANG . "_" . $module_data . "_cat` (`catid`, `title`, `alias`, `description`, `weight`, `status`) VALUES
(1, 'Giáo dục (Education)', 'giao-duc', 'Công cụ hỗ trợ giáo viên soạn bài, ra đề thi', 1, 1),
(2, 'Hành chính (Admin)', 'hanh-chinh', 'Công cụ soạn thảo văn bản hành chính, công văn', 2, 1);";
$db->query($sql_cat);

// Template 1: Lesson Plan Builder (Giáo dục)
// Demonstrates: Select with Value|Label, Textarea, Text
$prompt_body_1 = "Hãy đóng vai một giáo viên chuyên nghiệp. Soạn giáo án môn {subject} cho học sinh lớp {grade}.
Chủ đề bài học: {topic}.
Thời lượng: {duration} phút.
Phương pháp giảng dạy: {method}.
Yêu cầu chi tiết: {requirements}.

Cấu trúc giáo án cần bao gồm:
1. Mục tiêu bài học (Kiến thức, Kỹ năng, Thái độ).
2. Chuẩn bị (Giáo viên, Học sinh).
3. Tiến trình dạy học (Các hoạt động cụ thể).
4. Đánh giá và tổng kết.";

$input_config_1 = [
    [
        "label" => "Thông tin cơ bản",
        "key" => "",
        "type" => "section",
        "icon" => "",
        "required" => false,
        "options" => []
    ],
    [
        "label" => "Môn học",
        "key" => "subject",
        "type" => "text",
        "icon" => "fa-book",
        "required" => true,
        "options" => []
    ],
    [
        "label" => "Khối lớp",
        "key" => "grade",
        "type" => "select",
        "icon" => "fa-graduation-cap",
        "required" => true,
        "options" => [
            "1|Lớp 1",
            "2|Lớp 2",
            "3|Lớp 3",
            "4|Lớp 4",
            "5|Lớp 5",
            "6|Lớp 6",
            "7|Lớp 7",
            "8|Lớp 8",
            "9|Lớp 9",
            "10|Lớp 10",
            "11|Lớp 11",
            "12|Lớp 12"
        ]
    ],
    [
        "label" => "Chi tiết bài giảng",
        "key" => "",
        "type" => "group",
        "icon" => "fa-list-alt",
        "required" => false,
        "options" => []
    ],
    [
        "label" => "Tên bài/Chủ đề",
        "key" => "topic",
        "type" => "textarea",
        "icon" => "",
        "required" => true,
        "options" => []
    ],
    [
        "label" => "Thời lượng (phút)",
        "key" => "duration",
        "type" => "number",
        "icon" => "fa-clock-o",
        "required" => true,
        "options" => []
    ],
    [
        "label" => "Phương pháp",
        "key" => "method",
        "type" => "radio",
        "icon" => "",
        "required" => true,
        "options" => [
            "Truyền thống|Thuyết trình & Vấn đáp",
            "Nhóm|Thảo luận nhóm",
            "Dự án|Dạy học theo dự án",
            "Trực quan|Trực quan sinh động"
        ]
    ],
    [
        "label" => "Yêu cầu khác",
        "key" => "requirements",
        "type" => "textarea",
        "icon" => "",
        "required" => false,
        "options" => []
    ]
];

$sql_template_1 = "INSERT INTO `" . NV_PREFIXLANG . "_" . $module_data . "_templates` (`catid`, `title`, `alias`, `description`, `prompt_body`, `input_config`, `status`, `weight`, `add_time`, `edit_time`) VALUES
(1, 'Soạn Giáo Án', 'soan-giao-an', 'Hỗ trợ giáo viên soạn giáo án chi tiết theo chuẩn', :prompt_body, :input_config, 1, 1, " . NV_CURRENTTIME . ", " . NV_CURRENTTIME . ")";

$stmt1 = $db->prepare($sql_template_1);
$stmt1->bindValue(':prompt_body', $prompt_body_1, PDO::PARAM_STR);
$stmt1->bindValue(':input_config', json_encode($input_config_1, JSON_UNESCAPED_UNICODE), PDO::PARAM_STR);
$stmt1->execute();


// Template 2: Official Letter (Hành chính)
// Demonstrates: Checkbox (Multi-select), Key|Label usage for Tone
$prompt_body_2 = "Hãy viết một công văn hành chính gửi {receiver}.
Nội dung chính: {content}.
Lý do/Căn cứ: {reason}.
Giọng văn: {tone}.
Các mục cần nhấn mạnh: {highlights}.

Yêu cầu:
- Tuân thủ thể thức văn bản hành chính Việt Nam.
- Ngôn ngữ trang trọng, rõ ràng, súc tích.";

$input_config_2 = [
    [
        "label" => "Nơi nhận",
        "key" => "receiver",
        "type" => "text",
        "icon" => "fa-building",
        "required" => true,
        "options" => []
    ],
    [
        "label" => "Nội dung chính",
        "key" => "content",
        "type" => "textarea",
        "icon" => "fa-file-text-o",
        "required" => true,
        "options" => []
    ],
    [
        "label" => "Lý do / Căn cứ",
        "key" => "reason",
        "type" => "textarea",
        "icon" => "",
        "required" => false,
        "options" => []
    ],
    [
        "label" => "Tùy chọn nâng cao",
        "key" => "",
        "type" => "section",
        "icon" => "",
        "required" => false,
        "options" => []
    ],
    [
        "label" => "Giọng văn",
        "key" => "tone",
        "type" => "select",
        "icon" => "fa-pencil",
        "required" => true,
        "options" => [
            "trang trọng, nghiêm túc|Trang trọng (Mặc định)",
            "kiên quyết, cứng rắn|Kiên quyết (Từ chối/Nhắc nhở)",
            "nhẹ nhàng, thuyết phục|Thuyết phục (Đề xuất/Kêu gọi)",
            "khẩn cấp, hối thúc|Khẩn cấp"
        ]
    ],
    [
        "label" => "Nhấn mạnh",
        "key" => "highlights",
        "type" => "checkbox",
        "icon" => "fa-check-square-o",
        "required" => false,
        "options" => [
            "thời hạn hoàn thành|Thời hạn (Deadline)",
            "trách nhiệm pháp lý|Trách nhiệm pháp lý",
            "ngân sách thực hiện|Yếu tố ngân sách"
        ]
    ]
];

$sql_template_2 = "INSERT INTO `" . NV_PREFIXLANG . "_" . $module_data . "_templates` (`catid`, `title`, `alias`, `description`, `prompt_body`, `input_config`, `status`, `weight`, `add_time`, `edit_time`) VALUES
(2, 'Soạn Công Văn', 'soan-cong-van', 'Công cụ hỗ trợ soạn thảo công văn, tờ trình', :prompt_body, :input_config, 1, 2, " . NV_CURRENTTIME . ", " . NV_CURRENTTIME . ")";

$stmt2 = $db->prepare($sql_template_2);
$stmt2->bindValue(':prompt_body', $prompt_body_2, PDO::PARAM_STR);
$stmt2->bindValue(':input_config', json_encode($input_config_2, JSON_UNESCAPED_UNICODE), PDO::PARAM_STR);
$stmt2->execute();
