<?php
/**
 * khuVucSachMoi.php — HTML khu vực Sách mới phát hành
 * Yêu cầu: $ds_sachmoi (array), hàm hienThiTheSach()
 */
?>
<section class="new-releases" id="khu-vuc-sach-moi">
    <div class="section-header">
        <div>
            <h3><i class="fas fa-sparkles"></i> Sách Mới Phát Hành</h3>
            <p>Cập nhật những tựa sách mới nhất</p>
        </div>
<?php 
        // 1. Trường hợp: Vẫn còn sách để tải thêm
        if (count($ds_sachmoi) >= $limitSachMoi): 
        ?>
            <a href="index.php?limit_sm=<?= $limitSachMoi + 10 ?>#khu-vuc-sach-moi" class="view-all-btn light" style="text-decoration: none;">
                Xem thêm <i class="fas fa-chevron-down"></i>
            </a>

        <?php 
        // 2. Trường hợp: Đã tải hết sách trong database
        else: 
            // Nếu số lượng hiện tại đang nhiều hơn 10 cuốn thì hiện nút Thu gọn
            if ($limitSachMoi > 10): 
        ?>
            <div style="display: flex; align-items: center; gap: 15px;">
                <span style="color: #9ca3af; font-size: 14px;">Đã hiển thị tất cả sách mới</span>
                <a href="index.php?limit_sm=10#khu-vuc-sach-moi" class="view-all-btn light" style="text-decoration: none;">
                    Thu gọn <i class="fas fa-chevron-up"></i>
                </a>
            </div>
        <?php 
            else: 
            // Nếu ban đầu chỉ có dưới 10 cuốn thì không hiện nút Thu gọn
        ?>
            <span style="color: #9ca3af; font-size: 14px;">Đã hiển thị tất cả sách mới</span>
        <?php 
            endif;
        endif; 
        ?>
    </div>
    <div class="books-grid">
        <?php foreach ($ds_sachmoi as $sach):
            /* Badge cam: "Mới" */
            echo hienThiTheSach($sach, [
                ['class' => 'label-type', 'label' => 'Mới'],
            ]);
            //hiển thị mười sách, mỗi lần nhấn xem thêm sẽ tăng thêm mười sách, nếu không còn sách nào để tải thì ẩn nút xem thêm và hiện thông báo đã hiển thị tất cả sách mới
        endforeach; ?>
    </div>
</section>
