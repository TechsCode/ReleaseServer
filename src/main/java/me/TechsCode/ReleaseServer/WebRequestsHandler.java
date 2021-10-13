package me.TechsCode.ReleaseServer;

import me.TechsCode.ReleaseServer.github.GitHubUtil;
import org.springframework.core.io.UrlResource;
import org.springframework.http.HttpHeaders;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.io.File;
import java.net.MalformedURLException;
import java.util.List;
import java.util.Objects;
import java.util.Optional;
import java.util.stream.Collectors;

@RestController
public class WebRequestsHandler {

    @RequestMapping("/")
    public String index() {
        return "Im a happy server that moves around new software releases :)";
    }

    @GetMapping("/download/{project}/{tag}")
    public Object download(@RequestParam(value = "token", required = false) String token, @PathVariable(value = "project") String project, @PathVariable(value = "tag") String tag){
        if(token == null){
            return "You dont have any token provided!";
        }

        if(!Config.getInstance().getTokens().contains(token)){
            return "The token you have provided is invalid!";
        }

        List<Artifact> artifactsOfProject = ReleaseServer.getArtifacts().stream()
                .filter(x -> x.getRelease().getProject().getName().equalsIgnoreCase(project))
                .collect(Collectors.toList());

        if(artifactsOfProject.size() == 0){
            return "Could not find any artifact for project '"+project+"'";
        }

        if(tag.equals("latest")){
            String ghToken = "";
            if(artifactsOfProject.stream().findFirst().get().getRelease().getProject().getGithubToken().isPresent())
                ghToken = artifactsOfProject.stream().findFirst().get().getRelease().getProject().getGithubToken().get();
            String repo = artifactsOfProject.stream().findFirst().get().getRelease().getProject().getGithubRepository();
            tag = Objects.requireNonNull(GitHubUtil.getLatestRelease(ghToken, repo)).getRelease().getTagName();
        }

        String finalTag = tag;
        Optional<Artifact> artifact = artifactsOfProject.stream()
                .filter(x -> x.getReleaseTag().equals(finalTag)).findFirst();

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
                        .body(new UrlResource(asset.toURI()));
            } catch (MalformedURLException e) {
                e.printStackTrace();
                return "Error: "+e.getMessage();
            }
        } else {
            return "Could not find any artifact with the tag '"+tag+"'";
        }
    }
}


