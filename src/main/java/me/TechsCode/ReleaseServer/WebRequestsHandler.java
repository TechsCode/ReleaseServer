package me.TechsCode.ReleaseServer;

import org.springframework.core.io.Resource;
import org.springframework.core.io.UrlResource;
import org.springframework.http.HttpHeaders;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.io.File;
import java.net.MalformedURLException;
import java.util.Optional;

@RestController
public class WebRequestsHandler {

    @RequestMapping("/")
    public String index() {
        return "Im a happy server that moves around new software releases :)";
    }

    @GetMapping("/download/{project}/{tag}")
    public Object download(@RequestParam(value = "token") String token, @PathVariable(value = "project") String project, @PathVariable(value = "tag") String tag){
        if(token == null){
            return "You dont have any token provided!";
        }

        if(!Config.getInstance().getTokens().contains(token)){
            return "The token you have provided is invalid!";
        }

        Optional<Artifact> artifact = ReleaseServer.getArtifacts().stream()
                .filter(x -> x.getRelease().getProject().getName().equalsIgnoreCase(project))
                .filter(x -> x.getRelease().getUniqueTag().equals(tag))
                .findFirst();

        if(artifact.isPresent()){
            if(artifact.get().getAssets().length == 0){
                return "This artifact does not have any assets!";
            }

            if(artifact.get().getAssets().length > 1){
                return "This artifact has too many assets!";
            }

            try {
                File asset = artifact.get().getAssets()[0];

                return ResponseEntity.ok()
                        .contentType(MediaType.APPLICATION_OCTET_STREAM)
                        .header(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=\"" + asset.getName() + "\"")
                        .body(new UrlResource(asset.getPath()));
            } catch (MalformedURLException e) {
                e.printStackTrace();
                return "Error: "+e.getMessage();
            }
        } else {
            return "Could not find any artifact";
        }
    }
}


