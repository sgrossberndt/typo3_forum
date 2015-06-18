<?php

/*                                                                    - *
 *  COPYRIGHT NOTICE                                                    *
 *                                                                      *
 *  (c) 2012 Martin Helmich <m.helmich@mittwald.de>                     *
 *           Mittwald CM Service GmbH & Co KG                           *
 *           All rights reserved                                        *
 *                                                                      *
 *  This script is part of the TYPO3 project. The TYPO3 project is      *
 *  free software; you can redistribute it and/or modify                *
 *  it under the terms of the GNU General public License as published   *
 *  by the Free Software Foundation; either version 2 of the License,   *
 *  or (at your option) any later version.                              *
 *                                                                      *
 *  The GNU General public License can be found at                      *
 *  http://www.gnu.org/copyleft/gpl.html.                               *
 *                                                                      *
 *  This script is distributed in the hope that it will be useful,      *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of      *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the       *
 *  GNU General public License for more details.                        *
 *                                                                      *
 *  This copyright notice MUST APPEAR in all copies of the script!      *
 *                                                                      */



class Tx_Typo3Forum_Domain_Model_Moderation_ReportTest extends \Mittwald\Typo3Forum\Tests\Unit\BaseTestCase {



	/**
	 * @var \Mittwald\Typo3Forum\Domain\Model\Moderation\Report
	 */
	protected $fixture = NULL;



	public function setUp() {
		$this->fixture = new \Mittwald\Typo3Forum\Domain\Model\Moderation\Report();
	}



	public function testSetPostSetsPost() {
		$this->fixture->setPost($post = new \Mittwald\Typo3Forum\Domain\Model\Forum\Post());
		$this->assertTrue($this->fixture->getPost() === $post);
	}



	public function testSetReporterSetsReporter() {
		$this->fixture->setReporter($reporter = new \Mittwald\Typo3Forum\Domain\Model\User\FrontendUser());
		$this->assertTrue($this->fixture->getReporter() === $reporter);
	}



	public function testSetModeratorSetsModerator() {
		$this->fixture->setModerator($moderator = new \Mittwald\Typo3Forum\Domain\Model\User\FrontendUser());
		$this->assertTrue($this->fixture->getModerator() === $moderator);
	}



	public function testSetWorkflowStatusSetsWorkflowStatusIfNoneSet() {
		$status = new \Mittwald\Typo3Forum\Domain\Model\Moderation\ReportWorkflowStatus();
		$this->fixture->setWorkflowStatus($status);
		$this->assertTrue($this->fixture->getWorkflowStatus() === $status);
	}



	/**
	 * @depends testSetWorkflowStatusSetsWorkflowStatusIfNoneSet
	 */
	public function testSetWorkflowStatusSetsWorkflowStatusIfAllowed() {
		$status1 = new \Mittwald\Typo3Forum\Domain\Model\Moderation\ReportWorkflowStatus();
		$status2 = new \Mittwald\Typo3Forum\Domain\Model\Moderation\ReportWorkflowStatus();
		$status2->addAllowedFollowupStatus($status1);

		$this->fixture->setWorkflowStatus($status2);
		$this->fixture->setWorkflowStatus($status1);

		$this->assertTrue($this->fixture->getWorkflowStatus() === $status1);
	}



	public function testSetWorkflowStatusDoesNotSetWorkflowStatusIfNotAllowed() {
		$status1 = new \Mittwald\Typo3Forum\Domain\Model\Moderation\ReportWorkflowStatus();
		$status2 = new \Mittwald\Typo3Forum\Domain\Model\Moderation\ReportWorkflowStatus();

		$this->fixture->setWorkflowStatus($status2);
		$this->fixture->setWorkflowStatus($status1);

		$this->assertTrue($this->fixture->getWorkflowStatus() === $status2);
	}



	public function testAddCommentAddsComment() {
		$reporter = new \Mittwald\Typo3Forum\Domain\Model\User\FrontendUser();
		$this->fixture->addComment($comment = new \Mittwald\Typo3Forum\Domain\Model\Moderation\ReportComment($reporter, 'TEXT'));
		$this->assertContains($comment, $this->fixture->getComments());
	}



	/**
	 * @depends testAddCommentAddsComment
	 */
	public function testAddCommentSetsReportOnAddedComment() {
		$reporter = new \Mittwald\Typo3Forum\Domain\Model\User\FrontendUser();
		$this->fixture->addComment($comment = new \Mittwald\Typo3Forum\Domain\Model\Moderation\ReportComment($reporter, 'TEXT'));
		$this->assertTrue($comment->getReport() === $this->fixture);
	}



	/**
	 * @depends testAddCommentAddsComment
	 */
	public function testRemoveCommentRemovesComment() {
		$reporter = new \Mittwald\Typo3Forum\Domain\Model\User\FrontendUser();
		$this->fixture->addComment(new \Mittwald\Typo3Forum\Domain\Model\Moderation\ReportComment($reporter, 'TEXT'));
		$this->fixture->addComment($comment = new \Mittwald\Typo3Forum\Domain\Model\Moderation\ReportComment($reporter, 'TEXT'));
		$this->fixture->removeComment($comment);

		$this->assertEquals(1, count($this->fixture->getComments()));
		$this->assertNotContains($comment, $this->fixture->getComments());
	}



	/**
	 * @depends           testRemoveCommentRemovesComment
	 * @expectedException \Mittwald\Typo3Forum\Domain\Exception\InvalidOperationException
	 */
	public function testRemoveCommentDoesNotRemoveLastComment() {
		$reporter = new \Mittwald\Typo3Forum\Domain\Model\User\FrontendUser();
		$this->fixture->addComment($comment = new \Mittwald\Typo3Forum\Domain\Model\Moderation\ReportComment($reporter, 'TEXT'));
		$this->fixture->removeComment($comment);
	}



	/**
	 * @depends testSetPostSetsPost
	 */
	public function testGetTopicReturnsPostTopic() {
		$post  = new \Mittwald\Typo3Forum\Domain\Model\Forum\Post();
		$topic = new \Mittwald\Typo3Forum\Domain\Model\Forum\Topic();
		$topic->addPost($post);

		$this->fixture->setPost($post);
		$this->assertTrue($this->fixture->getTopic() === $topic);
	}



	/**
	 * @depends testAddCommentAddsComment
	 */
	public function testGetFirstCommentReturnsFirstComment() {
		$reporter = new \Mittwald\Typo3Forum\Domain\Model\User\FrontendUser();
		$this->fixture->addComment($comment = new \Mittwald\Typo3Forum\Domain\Model\Moderation\ReportComment($reporter, 'TEXT'));
		$this->fixture->addComment(new \Mittwald\Typo3Forum\Domain\Model\Moderation\ReportComment($reporter, 'TEXT'));

		$this->assertTrue($this->fixture->getFirstComment() === $comment);
	}

}