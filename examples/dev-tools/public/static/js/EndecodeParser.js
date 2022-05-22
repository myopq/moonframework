class EndecodeParser {
    setContent(content) {
        this.content = content
        this.current = false
    }

    reset() {
        $(".result").hide()
        this.current = false
    }

    isMache(content) {
        return false
    }

    isCurrent() {
        return this.current
    }
    
    setCurrent() {
        this.current = true
    }

    getParserName() {
        return this.name
    }

    do() {
        return
    }

}