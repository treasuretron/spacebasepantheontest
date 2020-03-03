<?php

namespace Drupal\spacebase_core\Plugin\QueueWorker;
/**
 * A Content Notification is sent on CRON run.
 *
 * @QueueWorker(
 *   id = "spacebase_content_notifications",
 *   title = @Translation("SpaceBase Cron Content Notifications"),
 *   cron = {"time" = 10}
 * )
 *
 * All work here is done in annoation above, so that cron will
 * processItems (see other plugin) with ID spacebase_content_notifications
 */
class CronContentNotifications extends ContentNotificationsBase {}
