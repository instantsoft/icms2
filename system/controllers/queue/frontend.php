<?php

class queue extends cmsFrontend {

    public function onCronRunQueue() {
        return cmsQueue::runJobs();
    }

}
