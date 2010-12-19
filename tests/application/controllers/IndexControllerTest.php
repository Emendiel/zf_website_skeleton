<?php

class IndexControllerTest extends ControllerTestCase {
    function testIndex() {
        $this->assertResponseCode(200);
    }
}