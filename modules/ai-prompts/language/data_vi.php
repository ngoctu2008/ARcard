<?php

/**
 * @Project NUKEVIET 4.x
 * @Author AIPrompts Team
 * @Copyright (C) 2024. All rights reserved
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE')) {
    die('Stop!!!');
}

// ---------------------------------------------------------
// 1. Categories
// ---------------------------------------------------------
$sql_cat = "INSERT INTO `" . NV_PREFIXLANG . "_" . $module_data . "_cat` (`catid`, `title`, `alias`, `description`, `weight`, `status`) VALUES
(1, 'Giáo dục', 'giao-duc', 'Các công cụ hỗ trợ giáo viên và nhà trường', 1, 1),
(2, 'Sáng tạo & Giải trí', 'sang-tao', 'Công cụ hỗ trợ sáng tạo nội dung và giải trí', 2, 1),
(3, 'Văn phòng & Công cụ', 'van-phong', 'Công cụ hỗ trợ công việc văn phòng và xử lý dữ liệu', 3, 1);";
$db->query($sql_cat);


// ---------------------------------------------------------
// 2. Templates (Tools)
// ---------------------------------------------------------

// Helper to insert template
if (!function_exists('insert_template')) {
    function insert_template($db, $module_data, $catid, $title, $alias, $icon, $desc, $prompt, $config, $weight) {
        $sql = "INSERT INTO `" . NV_PREFIXLANG . "_" . $module_data . "_templates`
                (`catid`, `title`, `alias`, `icon`, `description`, `prompt_body`, `input_config`, `status`, `weight`, `add_time`, `edit_time`)
                VALUES
                (:catid, :title, :alias, :icon, :description, :prompt_body, :input_config, 1, :weight, " . NV_CURRENTTIME . ", " . NV_CURRENTTIME . ")";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':catid', $catid, PDO::PARAM_INT);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':alias', $alias, PDO::PARAM_STR);
        $stmt->bindValue(':icon', $icon, PDO::PARAM_STR);
        $stmt->bindValue(':description', $desc, PDO::PARAM_STR);
        $stmt->bindValue(':prompt_body', $prompt, PDO::PARAM_STR);
        $stmt->bindValue(':input_config', json_encode($config, JSON_UNESCAPED_UNICODE), PDO::PARAM_STR);
        $stmt->bindValue(':weight', $weight, PDO::PARAM_INT);
        $stmt->execute();
    }
}

// ================= CATEGORY 1: GIÁO DỤC =================

// 1. Kế hoạch bài dạy (Lesson Plan)
$prompt = "Vai trò: Bạn là một chuyên gia sư phạm xuất sắc với phương pháp giảng dạy hiện đại (dạy học tích cực, lấy người học làm trung tâm). Bạn có khả năng thiết kế bài giảng chi tiết, logic và tuân thủ các chuẩn mực giáo dục hiện hành.
Nhiệm vụ: Hãy soạn thảo một Kế hoạch bài dạy (Giáo án) chi tiết dựa trên các thông tin tôi cung cấp dưới đây.
Thông tin đầu vào:
1. Tên bài học: {topic}
2. Môn học/Mô đun: {subject}
3. Đối tượng học viên: {grade}
4. Thời lượng: {duration} phút/tiết
5. Mục tiêu chính cần đạt: {objectives} (Kiến thức/Kỹ năng quan trọng nhất học viên phải làm được sau bài học)
6. Phương pháp dạy học chủ đạo: {method}
7. Thiết bị/Đồ dùng dạy học sẵn có: {equipment}

Yêu cầu về cấu trúc (Bắt buộc tuân thủ): Bài soạn cần chia thành các phần rõ ràng sau:
I. Mục tiêu bài học (Phân loại theo Bloom nếu có thể):
• Về kiến thức: Nêu rõ nội dung cần nhớ/hiểu.
• Về kỹ năng: Nêu rõ thao tác hoặc hành động người học thực hiện được (đặc biệt quan trọng nếu là dạy nghề).
• Về thái độ/phẩm chất: Tính cẩn thận, an toàn, hợp tác nhóm...
II. Chuẩn bị:
• Giáo viên cần chuẩn bị gì?
• Học sinh cần chuẩn bị gì?
III. Tiến trình dạy học (Quan trọng nhất): Hãy thiết kế thành 4 hoạt động chính theo chuỗi: Khởi động -> Hình thành kiến thức/Kỹ năng mới -> Luyện tập/Thực hành -> Vận dụng/Mở rộng.
Với mỗi hoạt động, hãy trình bày chi tiết theo cấu trúc:
1. Tên hoạt động & Thời gian:
2. Mục tiêu của hoạt động:
3. Nội dung hoạt động:
4. Sản phẩm dự kiến: (Học sinh/Học viên làm ra cái gì hoặc trả lời được gì).
5. Tổ chức thực hiện (Chia thành 4 bước):
o Bước 1: Chuyển giao nhiệm vụ (Giáo viên giao việc gì?).
o Bước 2: Thực hiện nhiệm vụ (Học sinh làm gì? Giáo viên hỗ trợ ra sao?).
o Bước 3: Báo cáo, thảo luận (Học sinh trình bày thế nào?).
o Bước 4: Kết luận, nhận định (Giáo viên chốt kiến thức/kỹ năng).
IV. Rút kinh nghiệm/Đánh giá:
• Gợi ý các tiêu chí đánh giá nhanh cuối giờ (Rubric hoặc câu hỏi trắc nghiệm ngắn).

Yêu cầu về văn phong:
• Ngôn ngữ sư phạm, rõ ràng, hướng dẫn cụ thể từng bước hành động.
• Sử dụng các động từ chỉ hành động (Mô tả, thực hiện, phân tích, lắp ráp...) để viết mục tiêu.";

$config = [
    ["label" => "Thông tin cơ bản", "type" => "section", "required" => false],
    ["label" => "Thông tin chung", "type" => "group", "icon" => "fa-info-circle", "required" => false],
    ["label" => "Môn học/Mô đun", "key" => "subject", "type" => "text", "required" => true],
    ["label" => "Đối tượng/Lớp", "key" => "grade", "type" => "select", "required" => true, "options" => ["Lớp 1", "Lớp 2", "Lớp 3", "Lớp 4", "Lớp 5", "Lớp 6", "Lớp 7", "Lớp 8", "Lớp 9", "Lớp 10", "Lớp 11", "Lớp 12", "Sơ cấp nghề", "Trung cấp nghề", "Cao đẳng nghề"]],
    ["label" => "Chi tiết bài giảng", "type" => "group", "icon" => "fa-list-alt", "required" => false],
    ["label" => "Tên bài học", "key" => "topic", "type" => "text", "required" => true],
    ["label" => "Thời lượng (phút/tiết)", "key" => "duration", "type" => "text", "required" => true],
    ["label" => "Mục tiêu chính", "key" => "objectives", "type" => "textarea", "required" => false, "description" => "Kiến thức/Kỹ năng quan trọng nhất"],
    ["label" => "Phương pháp", "key" => "method", "type" => "textarea", "required" => true, "description" => "Ví dụ: Thảo luận nhóm, Dạy học dự án, Làm mẫu..."],
    ["label" => "Thiết bị/Đồ dùng", "key" => "equipment", "type" => "textarea", "required" => false, "description" => "Máy chiếu, mô hình, vật tư..."]
];
insert_template($db, $module_data, 1, 'Kế hoạch bài dạy', 'ke-hoach-bai-day', 'fa-graduation-cap', 'Soạn giáo án chi tiết theo chuẩn mới (CV 5512/TCGD)', $prompt, $config, 1);

// 2. Bài tập (Exercises)
$prompt = "Tạo {quantity} bài tập {type} môn {subject} lớp {grade} về chủ đề: {topic}.
Độ khó: {difficulty}.
Yêu cầu:
- Bao gồm đáp án chi tiết.
- {requirement_plus}.";

$config = [
    ["label" => "Thông tin bài tập", "type" => "group", "icon" => "fa-pencil-square-o", "required" => false],
    ["label" => "Môn học", "key" => "subject", "type" => "text", "required" => true],
    ["label" => "Lớp", "key" => "grade", "type" => "select", "required" => true, "options" => ["Lớp 1", "Lớp 2", "Lớp 3", "Lớp 4", "Lớp 5", "Lớp 6", "Lớp 7", "Lớp 8", "Lớp 9", "Lớp 10", "Lớp 11", "Lớp 12"]],
    ["label" => "Chủ đề", "key" => "topic", "type" => "text", "required" => true],
    ["label" => "Dạng bài", "key" => "type", "type" => "radio", "required" => true, "options" => ["Trắc nghiệm khách quan", "Tự luận", "Điền khuyết", "Đúng/Sai"]],
    ["label" => "Cấu hình chi tiết", "type" => "group", "icon" => "fa-cogs", "required" => false],
    ["label" => "Số lượng câu", "key" => "quantity", "type" => "number", "required" => true],
    ["label" => "Độ khó", "key" => "difficulty", "type" => "select", "required" => true, "options" => ["Cơ bản (Nhận biết/Thông hiểu)", "Vận dụng", "Vận dụng cao", "Hỗn hợp"]],
    ["label" => "Yêu cầu thêm", "key" => "requirement_plus", "type" => "textarea", "required" => false]
];
insert_template($db, $module_data, 1, 'Bài tập', 'bai-tap', 'fa-pencil-square-o', 'Tạo ngân hàng câu hỏi trắc nghiệm và tự luận', $prompt, $config, 2);

// 3. Tình huống sư phạm
$prompt = "Đóng vai chuyên gia tâm lý giáo dục. Hãy tư vấn cách giải quyết tình huống sư phạm sau:
Đối tượng: {target}.
Tình huống: {situation}.
Mong muốn kết quả: {outcome}.

Hãy đưa ra:
1. Phân tích nguyên nhân tâm lý.
2. Các bước xử lý cụ thể (ngắn hạn và dài hạn).
3. Lời khuyên cho giáo viên/phụ huynh.";

$config = [
    ["label" => "Chi tiết tình huống", "type" => "group", "icon" => "fa-info", "required" => false],
    ["label" => "Đối tượng học sinh", "key" => "target", "type" => "text", "required" => true, "options" => ["Học sinh tiểu học", "Học sinh cá biệt", "Học sinh năng khiếu"]],
    ["label" => "Mô tả tình huống", "key" => "situation", "type" => "textarea", "required" => true],
    ["label" => "Kết quả mong muốn", "key" => "outcome", "type" => "textarea", "required" => false]
];
insert_template($db, $module_data, 1, 'Tình huống sư phạm', 'tinh-huong-su-pham', 'fa-users', 'Tư vấn giải quyết các vấn đề tâm lý học đường', $prompt, $config, 3);

// 4. Hoạt động giáo dục (HĐNGLL, Trải nghiệm)
$prompt = "Lên kế hoạch tổ chức Hoạt động giáo dục/Trải nghiệm cho học sinh {grade}.
Chủ đề: {topic}.
Hình thức tổ chức: {format}.
Thời gian: {time}.
Quy mô: {scale}.

Yêu cầu chi tiết kế hoạch gồm: Mục đích, Chuẩn bị, Tiến trình thực hiện, Dự trù kinh phí (nếu có).";

$config = [
    ["label" => "Thông tin hoạt động", "type" => "group", "icon" => "fa-info-circle", "required" => false],
    ["label" => "Khối lớp", "key" => "grade", "type" => "text", "required" => true],
    ["label" => "Chủ đề hoạt động", "key" => "topic", "type" => "text", "required" => true],
    ["label" => "Hình thức", "key" => "format", "type" => "select", "required" => true, "options" => ["Sinh hoạt dưới cờ", "Sinh hoạt lớp", "Tham quan dã ngoại", "Cuộc thi/Rung chuông vàng", "Câu lạc bộ"]],
    ["label" => "Chi tiết tổ chức", "type" => "group", "icon" => "fa-clock-o", "required" => false],
    ["label" => "Thời gian dự kiến", "key" => "time", "type" => "text", "required" => false],
    ["label" => "Quy mô", "key" => "scale", "type" => "text", "required" => false, "options" => ["Toàn trường", "Khối lớp", "Lớp học"]]
];
insert_template($db, $module_data, 1, 'Hoạt động giáo dục', 'hoat-dong-giao-duc', 'fa-building-o', 'Kế hoạch hoạt động trải nghiệm, ngoại khóa', $prompt, $config, 4);

// 5. Khảo sát
$prompt = "Tạo bảng câu hỏi khảo sát về chủ đề: {topic}.
Đối tượng khảo sát: {target}.
Mục đích: {purpose}.
Số lượng câu hỏi: {quantity}.
Dạng câu hỏi: {type}.";

$config = [
    ["label" => "Cấu hình khảo sát", "type" => "group", "icon" => "fa-check-square-o", "required" => false],
    ["label" => "Chủ đề khảo sát", "key" => "topic", "type" => "text", "required" => true],
    ["label" => "Đối tượng", "key" => "target", "type" => "text", "required" => true],
    ["label" => "Mục đích", "key" => "purpose", "type" => "textarea", "required" => false],
    ["label" => "Số lượng câu", "key" => "quantity", "type" => "number", "required" => true],
    ["label" => "Dạng câu hỏi", "key" => "type", "type" => "select", "required" => true, "options" => ["Trắc nghiệm (Có/Không)", "Thang đo Likert (1-5)", "Câu hỏi mở", "Hỗn hợp"]]
];
insert_template($db, $module_data, 1, 'Khảo sát', 'khao-sat', 'fa-check-square-o', 'Tạo phiếu khảo sát ý kiến', $prompt, $config, 5);

// 6. Sáng kiến (Kinh nghiệm)
$prompt = "Vai trò của bạn: Hãy đóng vai là một chuyên gia giáo dục (hoặc chuyên gia kỹ thuật) có 20 năm kinh nghiệm trong việc chấm thi và viết Sáng kiến kinh nghiệm. Bạn có khả năng tư duy logic, văn phong sư phạm, chuẩn mực, thuyết phục và sử dụng từ ngữ chuyên ngành chính xác.
Nhiệm vụ: Hãy viết cho tôi một đề cương chi tiết và nội dung đầy đủ cho một Sáng kiến kinh nghiệm dựa trên các thông tin đầu vào dưới đây.
Thông tin đầu vào:
1. Tên đề tài: {topic}
2. Lĩnh vực/Môn học: {field}
3. Đối tượng áp dụng: {target}
4. Thực trạng/Khó khăn ban đầu: {problem} (Mô tả ngắn gọn 2-3 khó khăn lớn nhất)
5. Giải pháp/Biện pháp chính: {solution} (Liệt kê các giải pháp đã làm)
6. Kết quả đạt được (định lượng/định tính): {result}

Yêu cầu về cấu trúc bài viết (Bắt buộc tuân thủ): Bài viết cần chia làm 3 phần chính rõ ràng:
Phần I: Đặt vấn đề
• Lý do chọn đề tài (Tính cấp thiết, lý luận và thực tiễn).
• Mục đích nghiên cứu.
• Đối tượng và phạm vi nghiên cứu.
Phần II: Giải quyết vấn đề (Nội dung chính - Cần viết sâu và chi tiết nhất)
• Cơ sở lý luận: Nêu ngắn gọn các lý thuyết liên quan.
• Thực trạng vấn đề: Phân tích kỹ ưu điểm và nhược điểm (nguyên nhân của hạn chế) trước khi áp dụng sáng kiến.
• Các biện pháp đã tiến hành: Đây là phần trọng tâm. Hãy trình bày thành các mục nhỏ (Biện pháp 1, Biện pháp 2, Biện pháp 3...). Mỗi biện pháp cần mô tả cách thức thực hiện chi tiết, có ví dụ minh họa cụ thể, cách làm mới mẻ và sáng tạo.
• Hiệu quả của sáng kiến: So sánh kết quả trước và sau khi áp dụng (Gợi ý bảng số liệu giả định nếu cần).
Phần III: Kết luận và kiến nghị
• Kết luận chung về ý nghĩa của sáng kiến.
• Bài học kinh nghiệm rút ra.
• Kiến nghị, đề xuất với cấp trên.

Yêu cầu về văn phong:
• Sử dụng ngôn ngữ hành chính, trang trọng, khách quan.
• Tránh dùng từ ngữ cảm thán, văn nói.
• Các luận điểm phải rõ ràng, gãy gọn.";

$config = [
    ["label" => "Thông tin chung", "type" => "group", "icon" => "fa-lightbulb-o", "required" => false],
    ["label" => "Tên đề tài Sáng kiến", "key" => "topic", "type" => "textarea", "required" => true],
    ["label" => "Lĩnh vực/Môn học", "key" => "field", "type" => "text", "required" => true],
    ["label" => "Đối tượng áp dụng", "key" => "target", "type" => "text", "required" => true, "options" => ["Học sinh lớp...", "Giáo viên tổ...", "Nhân viên phòng..."]],
    ["label" => "Nội dung sáng kiến", "type" => "group", "icon" => "fa-file-text", "required" => false],
    ["label" => "Thực trạng/Khó khăn", "key" => "problem", "type" => "textarea", "required" => true, "description" => "Mô tả 2-3 khó khăn lớn nhất trước khi áp dụng"],
    ["label" => "Giải pháp chính", "key" => "solution", "type" => "textarea", "required" => true, "description" => "Liệt kê các giải pháp, công cụ, phương pháp mới"],
    ["label" => "Kết quả đạt được", "key" => "result", "type" => "textarea", "required" => true, "description" => "Số liệu hoặc thay đổi tích cực sau khi áp dụng"]
];
insert_template($db, $module_data, 1, 'Sáng kiến', 'sang-kien', 'fa-lightbulb-o', 'Hỗ trợ viết Sáng kiến kinh nghiệm (SKKN) chuẩn mực', $prompt, $config, 6);

// 7. Định hướng nghề nghiệp
$prompt = "Đóng vai chuyên gia tư vấn hướng nghiệp. Hãy tư vấn nghề nghiệp cho học sinh dựa trên thông tin sau:
Sở thích: {interest}.
Sở trường/Năng lực: {strength}.
Các môn học giỏi: {subjects}.
Tính cách: {personality}.

Hãy đề xuất:
1. 3 nhóm ngành nghề phù hợp.
2. Các trường đại học/cao đẳng đào tạo tốt ngành này.
3. Lời khuyên phát triển bản thân.";

$config = [
    ["label" => "Hồ sơ học sinh", "type" => "group", "icon" => "fa-user", "required" => false],
    ["label" => "Sở thích", "key" => "interest", "type" => "textarea", "required" => true],
    ["label" => "Sở trường", "key" => "strength", "type" => "textarea", "required" => true],
    ["label" => "Môn học thế mạnh", "key" => "subjects", "type" => "text", "required" => true],
    ["label" => "Tính cách nổi bật", "key" => "personality", "type" => "text", "required" => false]
];
insert_template($db, $module_data, 1, 'Định hướng nghề nghiệp', 'dinh-huong-nghe-nghiep', 'fa-compass', 'Tư vấn hướng nghiệp cho học sinh', $prompt, $config, 7);


// ================= CATEGORY 2: SÁNG TẠO & GIẢI TRÍ =================

// 8. Tạo ảnh (Image Prompt)
$prompt = "Viết câu lệnh (prompt) chi tiết bằng tiếng Anh để tạo ảnh bằng AI (như Midjourney, Dall-E) với mô tả sau:
Chủ đề: {subject}.
Phong cách nghệ thuật: {style}.
Ánh sáng: {lighting}.
Góc máy: {angle}.
Màu sắc chủ đạo: {colors}.
Độ phân giải: --ar {ratio} --v 6.0";

$config = [
    ["label" => "Mô tả hình ảnh", "type" => "group", "icon" => "fa-picture-o", "required" => false],
    ["label" => "Mô tả hình ảnh", "key" => "subject", "type" => "textarea", "required" => true],
    ["label" => "Thông số kỹ thuật", "type" => "group", "icon" => "fa-sliders", "required" => false],
    ["label" => "Phong cách", "key" => "style", "type" => "select", "required" => true, "options" => ["Realistic (Thực tế)", "Anime/Manga", "Oil Painting (Tranh sơn dầu)", "Cyberpunk", "Cinematic (Điện ảnh)", "3D Render"]],
    ["label" => "Ánh sáng", "key" => "lighting", "type" => "select", "required" => false, "options" => ["Natural Light", "Cinematic Lighting", "Neon Lights", "Studio Lighting", "Golden Hour"]],
    ["label" => "Tỷ lệ khung hình", "key" => "ratio", "type" => "select", "required" => true, "options" => ["16:9|Ngang (16:9)", "9:16|Dọc (9:16)", "1:1|Vuông (1:1)", "4:3|Chuẩn (4:3)"]],
    ["label" => "Góc máy & Màu sắc", "key" => "angle", "type" => "text", "required" => false]
];
insert_template($db, $module_data, 2, 'Tạo ảnh', 'tao-anh', 'fa-picture-o', 'Tạo câu lệnh vẽ tranh AI chuyên nghiệp', $prompt, $config, 1);

// 9. Truyện tranh
$prompt = "Viết kịch bản chi tiết cho một cuốn truyện tranh ngắn ({pages} trang).
Thể loại: {genre}.
Cốt truyện chính: {plot}.
Nhân vật chính: {characters}.

Yêu cầu:
- Phân cảnh chi tiết từng trang (Page) và từng khung hình (Panel).
- Mô tả hình ảnh và lời thoại nhân vật trong từng khung.";

$config = [
    ["label" => "Kịch bản truyện", "type" => "group", "icon" => "fa-book", "required" => false],
    ["label" => "Thể loại", "key" => "genre", "type" => "text", "required" => true, "options" => ["Hành động", "Hài hước", "Kinh dị", "Đời thường", "Cổ tích"]],
    ["label" => "Cốt truyện", "key" => "plot", "type" => "textarea", "required" => true],
    ["label" => "Nhân vật", "key" => "characters", "type" => "textarea", "required" => true],
    ["label" => "Số lượng trang", "key" => "pages", "type" => "number", "required" => true]
];
insert_template($db, $module_data, 2, 'Truyện tranh', 'truyen-tranh', 'fa-book', 'Viết kịch bản và phân cảnh truyện tranh', $prompt, $config, 2);

// 10. Sáng tác nhạc
$prompt = "Hãy sáng tác một bài hát mới.
Thể loại: {genre}.
Chủ đề/Cảm xúc: {mood}.
Cấu trúc bài hát: {structure}.
Gợi ý về giai điệu/nhịp điệu: {rhythm}.

Yêu cầu:
- Viết lời bài hát (Lyrics) đầy đủ.
- Ghi chú hợp âm (Chords) cơ bản đi kèm.";

$config = [
    ["label" => "Yêu cầu bài hát", "type" => "group", "icon" => "fa-music", "required" => false],
    ["label" => "Thể loại nhạc", "key" => "genre", "type" => "select", "required" => true, "options" => ["Pop Ballad", "Rap/Hip-hop", "Bolero", "Nhạc thiếu nhi", "Rock"]],
    ["label" => "Chủ đề/Cảm xúc", "key" => "mood", "type" => "textarea", "required" => true],
    ["label" => "Cấu trúc", "key" => "structure", "type" => "select", "required" => true, "options" => ["Verse-Chorus-Verse-Chorus", "Verse-Chorus-Bridge-Chorus", "Tự do"]],
    ["label" => "Nhịp điệu/Gợi ý", "key" => "rhythm", "type" => "text", "required" => false]
];
insert_template($db, $module_data, 2, 'Sáng tác nhạc', 'sang-tac-nhac', 'fa-music', 'Sáng tác lời bài hát và hợp âm', $prompt, $config, 3);

// 11. Vẽ tranh (Hội họa - Ý tưởng)
$prompt = "Hãy đóng vai một họa sĩ/nhà phê bình nghệ thuật. Gợi ý ý tưởng sáng tác tranh vẽ về chủ đề: {topic}.
Chất liệu: {material}.
Phong cách: {style}.

Hãy mô tả chi tiết bức tranh cần vẽ: Bố cục, màu sắc, ý nghĩa ẩn dụ.";

$config = [
    ["label" => "Ý tưởng tranh", "type" => "group", "icon" => "fa-paint-brush", "required" => false],
    ["label" => "Chủ đề", "key" => "topic", "type" => "textarea", "required" => true],
    ["label" => "Chất liệu", "key" => "material", "type" => "text", "required" => true, "options" => ["Màu nước", "Sơn dầu", "Chì than", "Digital Art"]],
    ["label" => "Phong cách", "key" => "style", "type" => "text", "required" => false]
];
insert_template($db, $module_data, 2, 'Vẽ tranh', 've-tranh', 'fa-paint-brush', 'Tìm ý tưởng sáng tác hội họa', $prompt, $config, 4);

// 12. Làm phim
$prompt = "Viết kịch bản phim ngắn/video TikTok về chủ đề: {topic}.
Thời lượng: {duration}.
Thông điệp muốn truyền tải: {message}.
Đối tượng khán giả: {audience}.

Định dạng đầu ra: Bảng phân cảnh (Kịch bản phân cảnh) gồm: Số thứ tự, Cảnh quay (Góc máy), Nội dung (Hành động), Lời thoại/Âm thanh.";

$config = [
    ["label" => "Kịch bản phim", "type" => "group", "icon" => "fa-video-camera", "required" => false],
    ["label" => "Chủ đề", "key" => "topic", "type" => "textarea", "required" => true],
    ["label" => "Thời lượng", "key" => "duration", "type" => "text", "required" => true, "options" => ["Dưới 1 phút (Shorts/TikTok)", "3-5 phút", "10-15 phút"]],
    ["label" => "Thông điệp", "key" => "message", "type" => "textarea", "required" => true],
    ["label" => "Khán giả", "key" => "audience", "type" => "text", "required" => false]
];
insert_template($db, $module_data, 2, 'Làm phim', 'lam-phim', 'fa-film', 'Viết kịch bản phim, video ngắn', $prompt, $config, 5);

// 13. Games
$prompt = "Hãy thiết kế ý tưởng cho một trò chơi (Game Design Document rút gọn).
Thể loại: {genre}.
Cốt truyện/Bối cảnh: {setting}.
Cơ chế chơi (Gameplay): {gameplay}.
Nền tảng: {platform}.

Hãy mô tả:
1. Vòng lặp trò chơi (Core Loop).
2. Hệ thống nhân vật/vật phẩm.
3. Điều kiện thắng/thua.";

$config = [
    ["label" => "Thiết kế Game", "type" => "group", "icon" => "fa-gamepad", "required" => false],
    ["label" => "Thể loại Game", "key" => "genre", "type" => "text", "required" => true, "options" => ["RPG (Nhập vai)", "Puzzle (Giải đố)", "Action (Hành động)", "Strategy (Chiến thuật)", "Giáo dục"]],
    ["label" => "Bối cảnh", "key" => "setting", "type" => "textarea", "required" => true],
    ["label" => "Cơ chế chơi chính", "key" => "gameplay", "type" => "textarea", "required" => true],
    ["label" => "Nền tảng", "key" => "platform", "type" => "select", "required" => true, "options" => ["Mobile", "PC", "Web Game", "Board Game (Trò chơi bàn cờ)"]]
];
insert_template($db, $module_data, 2, 'Games', 'games', 'fa-gamepad', 'Thiết kế ý tưởng và kịch bản trò chơi', $prompt, $config, 6);

// 14. Viết bài truyền thông
$prompt = "Vai trò: Bạn là một chuyên gia Content Marketing và Truyền thông xã hội với tư duy ngôn từ sắc bén, khả năng lan tỏa cảm xúc và thấu hiểu tâm lý độc giả.
Nhiệm vụ: Hãy viết một bài truyền thông dựa trên các thông tin dưới đây.
Thông tin đầu vào:
1. Chủ đề/Sự kiện: {topic}
2. Mục đích bài viết: {purpose} (Thông báo, kêu gọi, tuyên dương, bán hàng...)
3. Đối tượng độc giả: {target}
4. Kênh đăng tải: {channel}
5. Thông điệp chính/Từ khóa (Key message): {message}
6. Thông tin chi tiết (5W1H): {details} (Thời gian, địa điểm, nội dung chính, số liệu...)
7. Giọng văn (Tone & Mood): {tone}

Yêu cầu về cấu trúc bài viết (Tuân thủ nghiêm ngặt):
1. Tiêu đề (Headline):
• Hãy đưa ra 3 phương án tiêu đề khác nhau để tôi lựa chọn (1 tiêu đề mang tính tin tức, 1 tiêu đề bắt trend/gợi sự tò mò, 1 tiêu đề đánh vào lợi ích người đọc).
2. Phần nội dung (Body):
• Nếu là Facebook/Zalo: Viết ngắn gọn (dưới 300 từ), chia đoạn rõ ràng, sử dụng các Emoji (biểu tượng cảm xúc) phù hợp để tăng tính tương tác.
• Nếu là Website/Báo: Viết theo cấu trúc tin tức (Sapo tóm tắt -> Diễn biến chi tiết -> Kết luận). Văn phong chuẩn mực, không dùng icon.
3. Lời kêu gọi hành động (Call to Action - CTA):
• Điều hướng người đọc làm gì tiếp theo? (VD: Nhấp vào link, Đến đăng ký ngay, Comment số điện thoại...).
4. Hashtag (Chỉ dành cho Mạng xã hội):
• Gợi ý 5-7 hashtag liên quan và đang thịnh hành.";

$config = [
    ["label" => "Thông tin cơ bản", "type" => "group", "icon" => "fa-info-circle", "required" => false],
    ["label" => "Chủ đề/Sự kiện", "key" => "topic", "type" => "textarea", "required" => true],
    ["label" => "Mục đích bài viết", "key" => "purpose", "type" => "select", "required" => true, "options" => ["Thông báo tin tức", "Tuyển sinh/Tuyển dụng", "Quảng bá sự kiện", "Chia sẻ kiến thức", "Bán hàng/Dịch vụ"]],
    ["label" => "Đối tượng độc giả", "key" => "target", "type" => "text", "required" => true],
    ["label" => "Kênh đăng tải", "key" => "channel", "type" => "select", "required" => true, "options" => ["Facebook/Zalo (Ngắn, icon)", "Website/Báo điện tử (Trang trọng)", "Email Marketing"]],
    ["label" => "Nội dung chi tiết", "type" => "group", "icon" => "fa-file-text-o", "required" => false],
    ["label" => "Thông điệp chính (Key message)", "key" => "message", "type" => "text", "required" => true, "description" => "Điều quan trọng nhất muốn người đọc nhớ"],
    ["label" => "Thông tin chi tiết (5W1H)", "key" => "details", "type" => "textarea", "required" => true, "description" => "Thời gian, địa điểm, thành phần, nội dung chính..."],
    ["label" => "Giọng văn", "key" => "tone", "type" => "select", "required" => true, "options" => ["Hài hước, bắt trend", "Trang trọng, chuyên nghiệp", "Cảm xúc, chia sẻ", "Gấp gáp, thôi thúc"]]
];
insert_template($db, $module_data, 2, 'Viết bài truyền thông', 'viet-bai-truyen-thong', 'fa-bullhorn', 'Soạn nội dung quảng cáo, PR đa kênh', $prompt, $config, 7);


// ================= CATEGORY 3: VĂN PHÒNG & CÔNG CỤ =================

// 15. Xử lý văn bản (Hành chính)
$prompt = "Hãy soạn thảo văn bản hành chính: {type}.
Nơi nhận: {receiver}.
Nội dung chính: {content}.
Lý do/Căn cứ: {reason}.
Yêu cầu về giọng văn: {tone}.

Đảm bảo đúng thể thức văn bản hành chính nhà nước hiện hành.";

$config = [
    ["label" => "Thông tin văn bản", "type" => "group", "icon" => "fa-file-text-o", "required" => false],
    ["label" => "Loại văn bản", "key" => "type", "type" => "select", "required" => true, "options" => ["Công văn", "Tờ trình", "Báo cáo", "Biên bản cuộc họp", "Thông báo", "Quyết định"]],
    ["label" => "Nơi nhận/Kính gửi", "key" => "receiver", "type" => "text", "required" => true],
    ["label" => "Nội dung chính", "key" => "content", "type" => "textarea", "required" => true],
    ["label" => "Lý do/Căn cứ", "key" => "reason", "type" => "textarea", "required" => false],
    ["label" => "Tùy chọn nâng cao", "type" => "group", "icon" => "fa-cog", "required" => false],
    ["label" => "Giọng văn", "key" => "tone", "type" => "select", "required" => true, "options" => ["Trang trọng", "Kiên quyết", "Thuyết phục", "Khẩn cấp"]]
];
insert_template($db, $module_data, 3, 'Xử lý văn bản', 'xu-ly-van-ban', 'fa-file-text', 'Soạn thảo công văn, báo cáo, tờ trình', $prompt, $config, 1);

// 16. Phân tích dữ liệu
$prompt = "Bạn là một chuyên gia phân tích dữ liệu (Data Analyst). Hãy phân tích dữ liệu sau đây:
Dữ liệu đầu vào: {data_desc}.
Mục tiêu phân tích: {goal}.

Hãy thực hiện:
1. Nhận xét xu hướng chung.
2. Chỉ ra các điểm bất thường (nếu có).
3. Dự báo hoặc đề xuất giải pháp dựa trên số liệu.
4. Gợi ý loại biểu đồ nên dùng để trực quan hóa.";

$config = [
    ["label" => "Dữ liệu phân tích", "type" => "group", "icon" => "fa-table", "required" => false],
    ["label" => "Mô tả dữ liệu", "key" => "data_desc", "type" => "textarea", "required" => true, "options" => [], "description" => "Copy số liệu hoặc mô tả bảng dữ liệu vào đây"],
    ["label" => "Mục tiêu phân tích", "key" => "goal", "type" => "textarea", "required" => true, "options" => ["Tìm xu hướng tăng trưởng", "So sánh hiệu quả", "Tìm nguyên nhân sụt giảm"]]
];
insert_template($db, $module_data, 3, 'Phân tích dữ liệu', 'phan-tich-du-lieu', 'fa-bar-chart', 'Phân tích số liệu và đưa ra nhận xét', $prompt, $config, 2);
