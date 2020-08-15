package me.TechsCode.ReleaseServer;

import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
public class WebRequestsHandler {

    @RequestMapping("/")
    public String index() {
        return "Im a happy server that moves around new software releases";
    }
}


