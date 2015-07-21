<?php $this->load->view('main_top_v');?>

<div class="container white_container">
    <div id="docs_wrap" class="row">
        <div class="col-lg-6">
            <h2>파트너 신청</h2>
            <p>푸시윙을 활용하여 무료로 푸시 알림을 보내고 당신의 웹사이트를 더욱 활성화 시키세요!</p>
            <form action="/main" method="post" class="marginTop30" style="width:80%">

                <h5>소속과 성명</h5>
                <input type="text" name="name" value="" class="form-control" />

                <h5>웹사이트 주소</h5>
                <input type="text" name="web_url" value="" class="form-control" />

                <h5>Email</h5>
                <input type="text" name="email" value="" class="form-control" />

                <input type="hidden" name="mode" value="partner">
                <div class="marginTop20"><input type="submit" value="신청하기" class="btn btn-primary btn-block" /></div>

            </form>

            <div id="restriction" class="marginTop50">
                <h4>다음과 같은 웹사이트는 등록되지 않습니다</h4>
                <ul>
                    <li>실정법에 위배되는 불법사이트나 청소년과 어린이에게 유해한 내용을 담은 홈페이지</li>
                    <li>크랙, 와레즈, 씨디키, 오토마우스 및 다른 저작권 소유자의 노래, 동영상, 책, 이미지 등을 제공하는 불법자료 제공사이트</li>
                    <li>스파이웨어, 바이러스 등에 노출되어 있거나 유포하는 사이트</li>
                    <li>합법적인 절차 및 관련 서류 없이 수입 건강식품, 프로그램, 상품, 게임 등을 불법 유통하는 사이트</li>
                    <li>주류, 담배, 총포, 도박, 카지노, 의약품 등 전자거래 금지 품목을 온라인에서 직접 구매, 결제 할 수 있는 홈페이지 (단, 주류, 담배의 경우 해외배송쇼핑몰로 국내 배송이 불가능한 경우 사이트 등록이 가능하며, 민속주는 서류 확인 후 등록여부를 검토하고 있습니다.)</li>
                    <li>음란사이트, 폭력물, 성인 컨텐츠 등을 담은 성인 사이트</li>
                    <li>특정 브랜드를 위조한 제품을 판매하거나 위조 제품 관련 컨텐츠를 제공하는 사이트</li>
                </ul>
                <p>* 사이트 등록 후에 불법으로 규정된 내용을 포함한 경우, 음란 성인사이트로 변질되는 경우에는 별도 통지 없이 등록취소 또는 삭제될 수 있습니다</p>
            </div>


        </div>
        <div class="col-lg-6">
            <h2>푸시윙 문의/제안</h2>
            <p>궁금하신 것이 있거나 기능 개선, 제휴 등 제안 사항이 있으시면 아래 양식을 이용하여 연락주세요. 최대한 빨리 확인하고 연락드리도록 하겠습니다.</p>
            <form action="/main" method="post" class="marginTop30" style="width:80%">

                <h5>소속과 성명</h5>
                <input type="text" name="name" value="" class="form-control" />

                <h5>Email</h5>
                <input type="text" name="email" value="" class="form-control" />

                <h5>구분</h5>
                <select name="category" class="form-control">
                    <option value="기타">기타</option>
                    <option value="제안">제안</option>
                    <option value="그누보드 새글 알림 플러그인 관련">그누보드 글 알림 플러그인 관련</option>
                    <option value="그누보드 댓글 알림 플러그인 관련">그누보드 댓글 알림 플러그인 관련</option>
                    <option value="XE 댓글 알림 애드온 관련">XE 댓글 알림 애드온 관련</option>
                    <option value="XE 새글 알림 애드온 관련">XE 새글 알림 애드온 관련</option>
                </select>

                <h5>내용</h5>
                <textarea name="content" class="form-control" rows="10"></textarea>

                <input type="hidden" name="mode" value="qna">
                <div class="marginTop20"><input type="submit" value="전송" class="btn btn-primary btn-block" /></div>

            </form>
        </div>
    </div>

</div><!-- /.container -->

<?php $this->load->view('main_bottom_v');?>